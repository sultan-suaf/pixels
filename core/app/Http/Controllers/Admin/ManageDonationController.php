<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;

class ManageDonationController extends Controller
{
    public function all()
    {
        $pageTitle = 'All Donations';
        $donations = $this->donationData();
        return view('admin.donation.log', compact('pageTitle', 'donations'));
    }

    public function pending()
    {
        $pageTitle = 'Pending Donation';
        $donations = $this->donationData('pending');
        return view('admin.donation.log', compact('pageTitle', 'donations'));
    }

    public function paid()
    {
        $pageTitle = 'Paid Donation';
        $donations = $this->donationData('paid');
        return view('admin.donation.log', compact('pageTitle', 'donations'));
    }
    public function reject()
    {
        $pageTitle = 'Rejected Donation';
        $donations = $this->donationData('Rejected');
        return view('admin.donation.log', compact('pageTitle', 'donations'));
    }

    protected function donationData($scope = null)
    {
        $donations = Donation::query();
        if ($scope) {
            $donations = Donation::$scope();
        }
        return $donations->searchable(['sender', 'user:username'])->with('user')->orderBy('id', 'desc')->paginate(getPaginate());
    }

    public function detail($id)
    {

        $donation = Donation::with('user', 'deposit')->find($id);
        $pageTitle = 'Donation Detail';
        return view('admin.donation.detail', compact('donation', 'pageTitle'));
    }



    public function setting()
    {
        $pageTitle = "Donation Setting";
        return view('admin.donation.setting', compact('pageTitle'));
    }

    public function updateSetting(Request $request)
    {
        $request->validate([
            "item"     => 'required',
            "subtitle" => 'required',
            "icon"     => 'required',
            "amount"   => 'required|numeric|gte:0'
        ]);

        $setting = gs();
        $setting->donation_setting = [
            'item'     => $request->item,
            'subtitle' => $request->subtitle,
            'icon'     => $request->icon,
            'amount'   => $request->amount,
        ];
        $setting->save();

        $notify[] = ['success', 'Donation setting successfully update.'];
        return back()->withNotify($notify);
    }
}
