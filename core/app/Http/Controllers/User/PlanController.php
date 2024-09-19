<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanPurchase;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PlanController extends Controller
{
    public function purchasePlan(Request $request)
    {
        $request->validate([
            'period'       => 'required|string|in:monthly,yearly',
            'plan'         => 'required|integer|gt:0',
            'payment_type' => 'required|string|in:direct,wallet'
        ], [
            'payment_type.in' => 'Select correct payment type'
        ]);

        $user = auth()->user();
        $plan = Plan::active()->where('id', $request->plan)->first();

        if (!$plan) {
            $notify[] = ['error', 'Plan not found!'];
            return back()->withNotify($notify);
        }

        $planPurchase = PlanPurchase::where('user_id', $user->id)->where('plan_id', $plan->id)->whereDate('expired_at', '>=', Carbon::now())->first();

        if ($planPurchase) {
            $notify[] = ['error', 'You already purchased the plan'];
            return back()->withNotify($notify);
        }

        if ($request->payment_type == 'wallet') {
            $price = $request->period . '_price';
            if ($plan->$price > $user->balance) {
                $notify[] = ['error', 'Insufficient Balance'];
                return back()->withNotify($notify);
            }

            $response = $this->purchase($plan, $request);

            $notify[] = ['success', 'Plan purchased successfully'];
            return to_route('invoice', ['plan',$response['trx'], $response['plan_id']])->withNotify($notify);
         
        } else {
        
            return to_route('user.payment', ['plan_id' => $plan->id, 'period' => $request->period]);
        }
    }
    protected function purchase($plan, $data)
    {
        $user  = auth()->user();
        $price = $data->period . '_price';
        $trx   = getTrx();
        $general = gs();

        //deduct balance
        $user->balance -= $plan->$price;
        $user->save();

        $planPurchase = PlanPurchase::where('user_id', $user->id)->first();
        if (!$planPurchase) {
            $planPurchase = new PlanPurchase();
        }

        //purchase plan
        $planPurchase->user_id         = $user->id;
        $planPurchase->plan_id         = $plan->id;
        $planPurchase->daily_limit     = $plan->daily_limit;
        $planPurchase->monthly_limit   = $plan->monthly_limit;
        $planPurchase->trx             = $trx;
        $planPurchase->amount          = $plan->$price;
        $planPurchase->purchase_date   = Carbon::now();

        if ($data->period == 'monthly') {
            $planPurchase->expired_at = Carbon::now()->addMonth();
        } else {
            $planPurchase->expired_at = Carbon::now()->addYear();
        }
        $planPurchase->save();

        //create transaction log
        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = $plan->$price;
        $transaction->post_balance = $user->balance;
        $transaction->trx_type     = '-';
        $transaction->trx          = $trx;
        $transaction->details      = 'Plan Purchased by' . $user->username;
        $transaction->remark       = 'plan_purchase';
        $transaction->save();


        //referral commission
        if ($general->referral_system && $user->ref_by) {
            referCommission($user, $plan->price, $transaction->trx);
        }

        //send notification
        notify($user, 'PLAN_PURCHASED', [
            'plan_name'    => $plan->name,
            'amount'       => showAmount($transaction->amount,currencyFormat:false),
            'trx'          => $trx,
            'charge'       => showAmount($transaction->charge,currencyFormat:false),
            'method_name'  => 'wallet balance',
            'post_balance' => showAmount($transaction->post_balance,currencyFormat:false),
            'expired_at'   => showDateTime($planPurchase->expired_at, 'F j, Y')
        ]);

        return ['plan_id' => $planPurchase->plan_id, 'trx' => $transaction->trx];
    }
}
