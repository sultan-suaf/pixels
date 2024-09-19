<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Download;
use App\Models\EarningLog;
use App\Models\NotificationLog;
use App\Models\PlanPurchase;
use App\Models\Transaction;
use App\Models\UserLogin;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function transaction(Request $request,$userId = null)
    {
        $pageTitle = 'Transaction Logs';

        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');

        $transactions = Transaction::searchable(['trx','user:username'])->filter(['trx_type','remark'])->dateFilter()->orderBy('id','desc')->with('user');
        if ($userId) {
            $transactions = $transactions->where('user_id',$userId);
        }
        $transactions = $transactions->paginate(getPaginate());

        return view('admin.reports.transactions', compact('pageTitle', 'transactions','remarks'));
    }

    public function loginHistory(Request $request)
    {
        $pageTitle = 'User Login History';
        $loginLogs = UserLogin::orderBy('id','desc')->searchable(['user:username'])->dateFilter()->with('user')->paginate(getPaginate());
        return view('admin.reports.logins', compact('pageTitle', 'loginLogs'));
    }

    public function loginIpHistory($ip)
    {
        $pageTitle = 'Login by - ' . $ip;
        $loginLogs = UserLogin::where('user_ip',$ip)->orderBy('id','desc')->with('user')->paginate(getPaginate());
        return view('admin.reports.logins', compact('pageTitle', 'loginLogs','ip'));
    }

    public function notificationHistory(Request $request){
        $pageTitle = 'Notification History';
        $logs = NotificationLog::orderBy('id','desc')->searchable(['user:username'])->dateFilter()->with('user')->paginate(getPaginate());
        return view('admin.reports.notification_history', compact('pageTitle','logs'));
    }

    public function emailDetails($id){
        $pageTitle = 'Email Details';
        $email = NotificationLog::findOrFail($id);
        return view('admin.reports.email_details', compact('pageTitle','email'));
    }
    public function downloadLog()
    {
        $pageTitle = "Download Logs";
        $logs = Download::orderBy('id', 'desc')->with('user', 'imageFile.image', 'contributor')->paginate(getPaginate());
        return view('admin.images.download_log', compact('pageTitle', 'logs'));
    }

    public function contributorEarningLog()
    {
        $pageTitle = 'Contributor\'s Earning Log';
        $logs      = EarningLog::orderBy('id', 'desc')->with('contributor', 'imageFile.image')->paginate(getPaginate());
        return view('admin.reports.contributor_earning_log', compact('pageTitle', 'logs'));
    }

    public function userImageCollectionLog()
    {
        $pageTitle = 'User\'s Image Collection';
        $collections = Collection::searchable(['title', 'user:username'])->with('user')->withCount('collectionImage')->orderBy('id', 'desc')->paginate(getPaginate());

        return view('admin.reports.user_image_collections', compact('pageTitle', 'collections'));
    }

    public function planPurchased()
    {
        $purchasedPlans = PlanPurchase::searchable(['user:username', 'user:firstname', 'user:lastname', 'plan:name'])->with('user', 'plan')->orderBy('id', 'DESC')->paginate(getPaginate());
        $pageTitle = 'Plan purchased log';
        return view('admin.reports.plan_purchased', compact('pageTitle', 'purchasedPlans'));
    }

    public function userImageCollectionFeatured($id)
    {
        return Collection::changeStatus($id, 'is_featured');
    }


    
}
