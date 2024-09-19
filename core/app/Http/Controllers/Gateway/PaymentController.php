<?php

namespace App\Http\Controllers\Gateway;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\Donation;
use App\Models\GatewayCurrency;
use App\Models\Plan;
use App\Models\PlanPurchase;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function deposit()
    {
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('name')->get();
        $pageTitle = 'Deposit Methods';
        return view('Template::user.payment.deposit', compact('gatewayCurrency', 'pageTitle'));
    }

    public function depositInsert(Request $request)
    {
        $request->validate([
            'type'        => 'required|in:payment,deposit',
            'amount' => 'required|numeric|gt:0',
            'gateway' => 'required',
            'currency' => 'required',
            'plan_id'     => 'required_if:type,payment|numeric|gt:0',
            'period'      => 'required_if:type,payment|in:monthly,yearly',
        ]);

        //for plan purchase
        $plan  = null;
        $amount = $request->amount;


        if ($request->plan_id) {
            $plan = Plan::active()->where('id', $request->plan_id)->first();
            if (!$plan) {
                $notify[] = ['error', 'Plan not found'];
                return back()->withNotify($notify);
            }

            $price = $request->period . '_price';

            if ($plan->$price != $amount) {
                $notify[] = ['error', 'Amount must be equal to plan\'s ' . $request->period . ' price'];
                return back()->withNotify($notify);
            }
            $amount = $plan->$price;
        }




        $user = auth()->user();
        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->where('method_code', $request->gateway)->where('currency', $request->currency)->first();
        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        if (!$request->plan_id) {

            if ($gate->min_amount > $amount || $gate->max_amount < $amount) {
                $notify[] = ['error', 'Please follow deposit limit'];
                return back()->withNotify($notify);
            }
        }

        $charge = $gate->fixed_charge + ($amount * $gate->percent_charge / 100);
        $payable = $amount + $charge;
        $finalAmount = $payable * $gate->rate;

        $data = new Deposit();
        $data->user_id = $user->id;
        $data->plan_id         = $request->plan_id ?? 0;
        $data->period          = $request->period ?? null;
        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount          = $amount;
        $data->charge = $charge;
        $data->rate = $gate->rate;
        $data->final_amount = $finalAmount;
        $data->btc_amount = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();
        $data->success_url = urlPath('user.deposit.history');
        $data->failed_url = urlPath('user.deposit.history');
        $data->save();
        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }


    //for plan purchase
    public function payment(Request $request)
    {
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('method_code')->get();

        $pageTitle = 'Payment Methods';
        $plan = Plan::active()->where('id', $request->plan_id)->first();

        if (!$plan) {
            $notify[] = ['error', 'Plan not found'];
            return back()->withNotify($notify);
        }

        $period = $request->period;

        return view('Template::user.payment.deposit', compact('gatewayCurrency', 'pageTitle', 'plan', 'period'));
    }

    public function depositConfirm()
    {
        $track = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->with('gateway')->firstOrFail();

        if ($deposit->method_code >= 1000) {
            if ($deposit->donation_id) {

                return to_route('donation.manual.confirm');
            }
            return to_route('user.deposit.manual.confirm');
        }

        $dirName = $deposit->gateway->alias;
        $new = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);


        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return back()->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        // for Stripe V3
        if (@$data->session) {
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Payment Confirm';
        $masterBlade = 'layouts.master';
        if (!auth()->check()) {
            if ($deposit->donation_id) $masterBlade = 'layouts.frontend';
        }
     
        return view("Template::$data->view", compact('data', 'pageTitle', 'deposit','masterBlade'));
    }


    public static function userDataUpdate($deposit, $isManual = null)
    {
        if ($deposit->status == Status::PAYMENT_INITIATE || $deposit->status == Status::PAYMENT_PENDING) {

            if ($deposit->donation_id) {
                $donation = Donation::find($deposit->donation_id);


                if ($donation->status == 0 || $donation->status == 2) {
                    $donation->status = 1;
                    $donation->save();
                    $receiver = User::find($donation->receiver_id);

                    //deposit status update
                    $deposit->status = Status::PAYMENT_SUCCESS;
                    $deposit->save();

                    $receiver->balance += $deposit->amount;
                    $receiver->save();

                    //transaction log
                    $transaction               = new Transaction();
                    $transaction->user_id      = $receiver->id;
                    $transaction->amount       = $deposit->amount;
                    $transaction->post_balance = $receiver->balance;
                    $transaction->charge       = $deposit->charge;
                    $transaction->trx_type     = '+';
                    $transaction->details      = "Donation received from " . @$donation->sender->name;
                    $transaction->trx          = $deposit->trx;
                    $transaction->remark       = 'donation';
                    $transaction->save();

                    // donation received  email
                    notify($receiver, 'DONATION_RECEIVE', [
                        'method_name'  => $donation->payment_info,
                        'amount'       => showAmount($donation->amount, currencyFormat: false),
                        'post_balance' => getAmount($receiver->balance),
                        'trx'          => $deposit->trx,
                        'sender_name'  => @$donation->sender->name,
                    ]);


                    $sender = [
                        'username' => @$donation->sender->name,
                        'email' => @$donation->sender->email,
                        'fullname' => @$donation->sender->name,
                        'mobileNumber' => @$donation->sender->mobileNumber,
                    ];

                    notify($sender, 'DONATION_SENT', [
                        'method_name' => $donation->payment_info,
                        'amount' => showAmount($donation->amount, currencyFormat: false),
                        'trx' => $deposit->trx,
                        'receiver_name' => $receiver->fullname,
                    ]);
                }
            } else {

                $deposit->status = Status::PAYMENT_SUCCESS;
                $deposit->save();

                $user = User::find($deposit->user_id);
                $user->balance += $deposit->amount;
                $user->save();

                $methodName = $deposit->methodName();

                $transaction = new Transaction();
                $transaction->user_id = $deposit->user_id;
                $transaction->amount = $deposit->amount;
                $transaction->post_balance = $user->balance;
                $transaction->charge = $deposit->charge;
                $transaction->trx_type = '+';
                $transaction->details = 'Deposit Via ' . $methodName;
                $transaction->trx = $deposit->trx;
                $transaction->remark = 'deposit';
                $transaction->save();


                if ($deposit->plan_id) {

                    $plan  = Plan::active()->where('id', $deposit->plan_id)->first();
                    if (!$plan) {
                        $notify[] = ['error', 'Plan not found'];
                        return to_route('plans')->withNotify($notify);
                    }

                    $user->balance -= $deposit->amount;
                    $user->save();

                    //transaction log
                    $transaction               = new Transaction();
                    $transaction->user_id      = $deposit->user_id;
                    $transaction->amount       = $deposit->amount;
                    $transaction->post_balance = $user->balance;
                    $transaction->charge       = 0;
                    $transaction->trx_type     = '-';
                    $transaction->details      = "Payment for the '{$plan->name}' plan.";
                    $transaction->trx          = $deposit->trx;
                    $transaction->remark       = 'payment';
                    $transaction->save();

                    // referral commission
                    if (gs('referral_system') && $user->ref_by) {
                        referCommission($user, $deposit->amount, $deposit->trx);
                    }

                    $planPurchase = PlanPurchase::where('user_id', $user->id)->first();
                    if (!$planPurchase) {
                        $planPurchase = new PlanPurchase();
                    }

                    //purchase plan
                    $planPurchase->user_id         = $user->id;
                    $planPurchase->plan_id         = $plan->id;
                    $planPurchase->daily_limit     = $plan->daily_limit;
                    $planPurchase->monthly_limit   = $plan->monthly_limit;
                    $planPurchase->trx             = $deposit->trx;
                    $planPurchase->amount          = $deposit->amount;
                    $planPurchase->purchase_date   = Carbon::now();
                    if ($deposit->period == 'monthly') {
                        $planPurchase->expired_at = Carbon::now()->addMonth();
                    } else {
                        $planPurchase->expired_at = Carbon::now()->addYear();
                    }
                    $planPurchase->save();

                    session()->put('PLAN_PURCHASE', [
                        'trx' => $deposit->trx,
                        'plan_id' => $plan->id
                    ]);
                }



                if (!$isManual) {
                    $adminNotification = new AdminNotification();
                    $adminNotification->user_id = $user->id;
                    $adminNotification->title = 'Deposit successful via ' . $methodName;
                    $adminNotification->click_url = urlPath('admin.deposit.successful');
                    $adminNotification->save();
                }

                notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                    'method_name' => $methodName,
                    'method_currency' => $deposit->method_currency,
                    'method_amount' => showAmount($deposit->final_amount, currencyFormat: false),
                    'amount' => showAmount($deposit->amount, currencyFormat: false),
                    'charge' => showAmount($deposit->charge, currencyFormat: false),
                    'rate' => showAmount($deposit->rate, currencyFormat: false),
                    'trx' => $deposit->trx,
                    'post_balance' => showAmount($user->balance)
                ]);

                if (isset($plan)) {
                    notify($user, $isManual ? 'PURCHASE_REQUEST_APPROVE' : 'PLAN_PURCHASED', [
                        'plan_name'    => $plan->name,
                        'amount'       => showAmount($transaction->amount, currencyFormat: false),
                        'trx'          => $transaction->trx,
                        'charge'       => showAmount($transaction->charge, currencyFormat: false),
                        'method_name'  => $deposit->gatewayCurrency()->name,
                        'post_balance' => showAmount($transaction->post_balance,  currencyFormat: false),
                        'expired_at'   => showDateTime($planPurchase->expired_at, 'F j, Y')
                    ]);
                }
            }
        }
    }

    public function manualDepositConfirm()
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        if ($data->method_code > 999) {
            if ($data->donation_id) {
                $pageTitle = 'Donation Confirm';
            } else {
                $pageTitle = ($data->plan_id ? 'Payment' : 'Deposit') . ' Confirm';
            }
            $method = $data->gatewayCurrency();
            $gateway = $method->method;

            $masterBlade = 'layouts.master';

            if (!auth()->check()) {

                if ($data->donation_id) $masterBlade = 'layouts.frontend';
            }

            return view('Template::user.payment.manual', compact('data', 'pageTitle', 'method', 'gateway', 'masterBlade'));
        }
        abort(404);
    }

    public function manualDepositUpdate(Request $request)
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        if ($data->donation_id) {

            $donation = Donation::find($data->donation_id);
            $donation->status = Status::PAYMENT_PENDING;
            $donation->save();
        }



        $image              = @$data?->donation?->first()?->image;
        $failedRedirectUrl  = @$image ? route('image.detail', [slug(@$image->title), @$image->id]) : $data->failed_url;

        if (!$data) {
            return to_route($failedRedirectUrl);
        }


        $gatewayCurrency = $data->gatewayCurrency();
        $gateway = $gatewayCurrency->method;
        $formData = $gateway->form->form_data;

        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);


        $data->detail = $userData;
        $data->status = Status::PAYMENT_PENDING;
        $data->save();

        $url        = urlPath('admin.deposit.details', $data->id);

        if ($data->donation_id) {

            $message    = 'Your donation request has been taken';
        } else {
            $message    = 'Your deposit request has been taken';
        }

        $notifyType = 'DEPOSIT_REQUEST';

        $notifyBody = [
            'method_name'     => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount'   => showAmount($data->final_amount),
            'amount'          => showAmount($data->amount),
            'charge'          => showAmount($data->charge),
            'rate'            => showAmount($data->rate),
            'trx'             => $data->trx
        ];

        if ($data->plan_id) {
            $plan       = Plan::active()->where('id', $data->plan_id)->first();
            if (!$plan) {
                $notify[] = ['error', 'Plan not found'];
                return to_route('plans')->withNotify($notify);
            }

            $url        = urlPath('admin.deposit.details', $data->id);
            $message    = 'Your payment request has been taken';
            $notifyType = 'PAYMENT_REQUEST';

            $notifyBody = [
                'plan_name' => $plan->name,
                'method_name' => $data->gatewayCurrency()->name,
                'method_currency' => $data->method_currency,
                'method_amount' => showAmount($data->final_amount,currencyFormat:false),
                'amount' => showAmount($data->amount,currencyFormat:false),
                'charge' => showAmount($data->charge,currencyFormat:false),
                'rate' => showAmount($data->rate,currencyFormat:false),
                'trx' => $data->trx
            ];

            session()->put('PLAN_PURCHASE', [
                'trx' =>  $data->trx,
                'plan_id' => $plan->id
            ]);
        }


        if (!$data->donation_id) {
            $adminNotification            = new AdminNotification();
            $adminNotification->user_id   = $data->user->id;
            $adminNotification->title     = ($data->plan_id ? 'Payment' : 'Deposit') . ' request from ' . $data->user->username;
            $adminNotification->click_url = $url;
            $adminNotification->save();
            notify($data->user, $notifyType, $notifyBody);
            $notify[] = ['success', $message];
            return to_route('user.deposit.history')->withNotify($notify);
        } else {

            $adminNotification            = new AdminNotification();
            $adminNotification->user_id   = $data->donation->receiver_id;
            $adminNotification->title     =  ' Donation from ' . $data->donation->sender->name;
            $adminNotification->click_url = urlPath('admin.donation.detail', $data->donation->id);;
            $adminNotification->save();
            notify($data->user, $notifyType, $notifyBody);
            $notify[] = ['success', $message];
            return to_route('image.detail', [slug(@$image->title), @$image->id])->withNotify($notify);
        }
    }
}
