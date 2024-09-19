<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\Deposit;
use App\Models\Donation;
use App\Models\GatewayCurrency;
use App\Models\Image;
use App\Models\ImageFile;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function donationInsert(Request $request, $imageId)
    {
        abort_if(!gs('donation_module'), 404);
        $image          = Image::find($imageId);
        if (auth()->check()) {

            abort_if(@$image->user_id == auth()->user()->id, 404);
        }
        $freeFileExists = ImageFile::where('image_id', $imageId)->exists();
        if (!$freeFileExists) {
            $notify[] = ['error', 'This image has no free file'];
            $notify[] = ['info', 'Your donations are exclusively for free image creators'];
            return back()->withNotify($notify)->withInput($request->all());
        }
        abort_if($request->gateway == 'balance' && !auth()->check(), 404);
        $gateway           = null;
       
        $donationAmount    = $request->donation_quantity * @gs('donation_setting')?->amount;
        if ($request->gateway == 'balance') {
            if (auth()->user()->balance < $donationAmount) {
                $notify[] = ['error', 'Insufficient balance'];
                return back()->withNotify($notify)->withInput($request->all());
            }
        } else {
            $gateway = GatewayCurrency::whereHas('method', function ($gate) {
                $gate->where('status', Status::ENABLE);
            })->where('method_code', $request->gateway)->where('currency', $request->currency)->first();
            if (!$gateway) {
                $notify[] = ['error', 'Invalid gateway'];
                return back()->withNotify($notify)->withInput($request->alL());
            }
        }
        $validationCheck = auth()->check() ? 'nullable' : 'required';
        $user = auth()->user();

        $request->validate([
            'gateway'           => 'required',
            'donation_quantity' => 'required',
            'name'              => $validationCheck,
            'email'             => $validationCheck . '|email',
            'mobile'            => $validationCheck,
        ]);

        $donation               = new Donation();
        $donation->receiver_id  = $image->user_id;
        $donation->image_id     = $imageId;
        $donation->amount       = $donationAmount;
        $donation->payment_info = '';
        $donation->sender       = [
            'name'   => $user->fullname ?? $request->name,
            'email'  =>  $user->email ?? $request->email,
            'mobile' =>  $user->mobileNumber ?? $request->mobile,
        ];
        $donation->save();

        if ($request->gateway == 'balance') {
            return $this->donateViaWalletBalance($donation, $image->user_id, $gateway);
        } else {
            return $this->donateViaGateway($donation, $image->user_id, $gateway);
        }
    }
    private function donateViaWalletBalance($donation, $receiverId, $gateway)
    {
        $receiver = User::find($receiverId);

        $sender = auth()->user();
        $sender->balance -= $donation->amount;
        $sender->save();
        $trx = getTrx();

        $batchTransactions = [];

        $transaction                 = [];
        $transaction['user_id']      = $sender->id;
        $transaction['amount']       = $donation->amount;
        $transaction['post_balance'] = getAmount($sender->balance);
        $transaction['charge']       = 0;
        $transaction['trx_type']     = '-';
        $transaction['details']      = "Charge for donation to " . $receiver->fullname;
        $transaction['trx']          = $trx;
        $transaction['remark']       = 'donation';
        $transaction['created_at']   = now();
        $transaction['updated_at']   = now();

        $batchTransactions[]         = $transaction;


        $receiver->balance += $donation->amount;
        $receiver->save();

        $transaction               = [];
        $transaction['user_id']      = $sender->id;
        $transaction['amount']       = $donation->amount;
        $transaction['post_balance'] = getAmount($sender->balance);
        $transaction['charge']       = 0;
        $transaction['trx_type']     = '-';
        $transaction['details']      = "Donation received from " . @$donation->sender->name;
        $transaction['trx']          = $trx;
        $transaction['remark']       = 'donation';
        $transaction['created_at']   = now();
        $transaction['updated_at']   = now();
        $batchTransactions[]  = $transaction;

        Transaction::insert($batchTransactions);



        $donation->payment_info = 'Wallet balance';
        $donation->status       = 1;
        $donation->save();

        // Email Notification Receiver


        notify($receiver, 'DONATION_RECEIVE', [
            'method_name'  => $donation->payment_info,
            'amount'       => showAmount($donation->amount, currencyFormat:false),
            'post_balance' => getAmount($receiver->balance),
            'trx'          => $trx,
            'sender_name'  => @$donation->sender->name,
        ]);

        //Email Notification Sender

        notify($sender, 'DONATION_SENT', [
            'method_name'   => $donation->payment_info,
            'amount'        => showAmount($donation->amount, currencyFormat:false),
            'trx'           => $trx,
            'receiver_name' => @$receiver->fullname,
        ]);

        $notify[] = ['success', 'You have donated successfully'];
        return back()->withNotify($notify);
    }
    private function donateViaGateway($donation, $receiverId, $gateway)
    {

        $donation->payment_info = $gateway->name;
        $donation->save();

        $charge    = $gateway->fixed_charge + ($donation->amount * $gateway->percent_charge / 100);
        $payable   = $donation->amount + $charge;
        $final_amount = $payable * $gateway->rate;

        $deposit                  = new Deposit();
        $deposit->donation_id     = $donation->id;
        $deposit->method_code     = $gateway->method_code;
        $deposit->method_currency = strtoupper($gateway->currency);
        $deposit->amount          = $donation->amount;
        $deposit->charge          = $charge;
        $deposit->rate            = $gateway->rate;
        $deposit->final_amount       = $final_amount;
        $deposit->btc_amount         = 0;
        $deposit->btc_wallet      = "";
        $deposit->trx             = getTrx();
        $deposit->success_url =  urlPath('image.detail',[slug($donation->image->title), $donation->image_id] ) ;
        $deposit->failed_url = urlPath('image.detail',[slug($donation->image->title), $donation->image_id] ) ;
        $deposit->save();
        session()->put('Track', $deposit->trx);

        return to_route('donation.confirm');
    }
}
