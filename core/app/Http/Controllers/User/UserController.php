<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\GoogleAuthenticator;
use App\Models\DeviceToken;
use App\Models\Donation;
use App\Models\Download;
use App\Models\EarningLog;
use App\Models\Follow;
use App\Models\Form;
use App\Models\ReferralLog;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function home()
    {
        $user           = auth()->user()->load('purchasedPlan.plan', 'downloads');
        $pageTitle      = 'Dashboard';
        $report['date'] = collect([]);

        $earningMonth = EarningLog::where('earning_date', '>=', Carbon::now()->subMonth())
            ->where('contributor_id', $user->id)
            ->selectRaw("SUM(CASE WHEN contributor_id = $user->id THEN amount END) as totalAmount")
            ->selectRaw("DATE_FORMAT(earning_date,'%M-%d') as date")
            ->orderBy('date')
            ->groupBy('date')->get();

        $earningMonth->map(function ($earningData) use ($report) {
            $report['date']->push($earningData->date);
        });

        $report['date'] = dateSorting($report['date']->unique()->toArray());
        return view('Template::user.dashboard', compact('pageTitle', 'report', 'earningMonth', 'user'));
    }

    public function depositHistory(Request $request)
    {
        if (session()->has('PLAN_PURCHASE')) {
            $session = session()->get('PLAN_PURCHASE');
            $trx     = $session['trx'];
            $id      = $session['plan_id'];
            session()->forget('PLAN_PURCHASE');
            return to_route('invoice', ['type' => 'plan', 'trx' => $trx, 'id' => $id]);
        }

        $pageTitle = 'Deposit History';
        $deposits  = auth()->user()->deposits()->searchable(['trx'])->with(['gateway'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::user.deposit_history', compact('pageTitle', 'deposits'));
    }

    public function show2faForm()
    {
        $ga        = new GoogleAuthenticator();
        $user      = auth()->user();
        $secret    = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . gs('site_name'), $secret);
        $pageTitle = '2FA Security';
        return view('Template::user.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'key'  => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($user, $request->code, $request->key);
        if ($response) {
            $user->tsc = $request->key;
            $user->ts  = Status::ENABLE;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator activated successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }

    public function disable2fa(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);

        $user     = auth()->user();
        $response = verifyG2fa($user, $request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts  = Status::DISABLE;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator deactivated successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }

    public function transactions()
    {
        $pageTitle = 'Transactions';
        $remarks   = Transaction::distinct('remark')->orderBy('remark')->get('remark');

        $transactions = Transaction::where('user_id', auth()->id())->searchable(['trx'])->filter(['trx_type', 'remark'])->orderBy('id', 'desc')->paginate(getPaginate());

        return view('Template::user.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }

    public function kycForm()
    {
        if (auth()->user()->kv == Status::KYC_PENDING) {
            $notify[] = ['error', 'Your KYC is under review'];
            return to_route('user.home')->withNotify($notify);
        }
        if (auth()->user()->kv == Status::KYC_VERIFIED) {
            $notify[] = ['error', 'You are already KYC verified'];
            return to_route('user.home')->withNotify($notify);
        }
        $pageTitle = 'KYC Form';
        $form      = Form::where('act', 'kyc')->first();
        return view('Template::user.kyc.form', compact('pageTitle', 'form'));
    }

    public function kycData()
    {
        $user      = auth()->user();
        $pageTitle = 'KYC Data';
        return view('Template::user.kyc.info', compact('pageTitle', 'user'));
    }

    public function kycSubmit(Request $request)
    {
        $form           = Form::where('act', 'kyc')->firstOrFail();
        $formData       = $form->form_data;
        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $user = auth()->user();
        foreach (@$user->kyc_data ?? [] as $kycData) {
            if ($kycData->type == 'file') {
                fileManager()->removeFile(getFilePath('verify') . '/' . $kycData->value);
            }
        }
        $userData                   = $formProcessor->processFormData($request, $formData);
        $user->kyc_data             = $userData;
        $user->kyc_rejection_reason = null;
        $user->kv                   = Status::KYC_PENDING;
        $user->save();

        $notify[] = ['success', 'KYC data submitted successfully'];
        return to_route('user.home')->withNotify($notify);
    }

    public function userData()
    {
        $user = auth()->user();

        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }

        $pageTitle  = 'User Data';
        $info       = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = @implode(',', $info['code']);
        $countries  = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view('Template::user.user_data', compact('pageTitle', 'user', 'countries', 'mobileCode'));
    }

    public function userDataSubmit(Request $request)
    {

        $user = auth()->user();

        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }

        $countryData  = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes  = implode(',', array_column($countryData, 'dial_code'));
        $countries    = implode(',', array_column($countryData, 'country'));

        $request->validate([
            'country_code' => 'required|in:' . $countryCodes,
            'country'      => 'required|in:' . $countries,
            'mobile_code'  => 'required|in:' . $mobileCodes,
            'username'     => 'required|unique:users|min:6',
            'mobile'       => ['required', 'regex:/^([0-9]*)$/', Rule::unique('users')->where('dial_code', $request->mobile_code)],
        ]);


        if (preg_match("/[^a-z0-9_]/", trim($request->username))) {
            $notify[] = ['info', 'Username can contain only small letters, numbers and underscore.'];
            $notify[] = ['error', 'No special character, space or capital letters in username.'];
            return back()->withNotify($notify)->withInput($request->all());
        }

        $user->country_code = $request->country_code;
        $user->mobile       = $request->mobile;
        $user->username     = $request->username;


        $user->address      = $request->address;
        $user->city         = $request->city;
        $user->state        = $request->state;
        $user->zip          = $request->zip;
        $user->country_name = @$request->country;
        $user->dial_code    = $request->mobile_code;

        $user->profile_complete = Status::YES;
        $user->save();

        return to_route('user.home');
    }


    public function addDeviceToken(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()->all()];
        }

        $deviceToken = DeviceToken::where('token', $request->token)->first();

        if ($deviceToken) {
            return ['success' => true, 'message' => 'Already exists'];
        }

        $deviceToken          = new DeviceToken();
        $deviceToken->user_id = auth()->user()->id;
        $deviceToken->token   = $request->token;
        $deviceToken->is_app  = Status::NO;
        $deviceToken->save();

        return ['success' => true, 'message' => 'Token saved successfully'];
    }

    public function downloadAttachment($fileHash)
    {
        $filePath  = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $title     = slug(gs('site_name')) . '- attachments.' . $extension;
        try {
            $mimetype = mime_content_type($filePath);
        } catch (\Exception $e) {
            $notify[] = ['error', 'File does not exists'];
            return back()->withNotify($notify);
        }
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    public function downloadHistory()
    {
        $pageTitle = 'Download History';
        $downloads = Download::where('user_id', auth()->id())->with('imageFile.image:title,id,category_id', 'imageFile.image.category:id,name', 'contributor', 'imageFile')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::user.download_history', compact('pageTitle', 'downloads'));
    }


    public function updateFollow(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'following_id'  => 'required|exists:users,id',
            'status'        => 'required|between:0,1',
            'append_status' => 'required|between:0,1'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }
        $user   = auth()->user();
        $follow = Follow::where('following_id', $request->following_id)->where('user_id', $user->id)->first();
        if ($request->status) {
            if (!$follow) {
                $follow               = new Follow();
                $follow->user_id      = $user->id;
                $follow->following_id = $request->following_id;
                $follow->save();
            }
        } else {
            if ($follow) {
                $follow->delete();
            }
        }
        if ($request->append_status) {
            $member = User::where('id', $request->following_id)->with(['followers' => function ($followers) {
                $followers->orderBy('id', 'desc')->limit(21);
            }])->withCount('followers')->first();
            $users    = $member->followers;
            $relation = 'followerProfile';

            $renderedHtml = view('Template::partials.follower_following_avatar', compact('users', 'relation'))->render();

            return response()->json(['html' => $renderedHtml, 'total_followers' => $member->followers_count]);
        }

        return response()->json(['status' => 'success']);
    }

    public function earningLog()
    {
        $pageTitle = 'Earning Logs';
        $logs      = EarningLog::where('contributor_id', auth()->id())->with('imageFile')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::user.earning_logs', compact('pageTitle', 'logs'));
    }


    public function donationHistory()
    {
        $pageTitle = "Donation History";
        $donations = Donation::where('receiver_id', auth()->user()->id)->where('status', Status::PAYMENT_SUCCESS)->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::user.donation_log', compact('donations', 'pageTitle'));
    }


    public function referrals()
    {
        $user      = User::where('id', auth()->id())->with('allReferrals')->firstOrFail();
        $pageTitle = 'Referrals';
        return view('Template::user.referral.all', compact('pageTitle', 'user'));
    }

    public function referralCommissionLog()
    {
        $general = gs();

        if (!$general->referral_system) {
            $notify[] = ['error', 'Sorry, the referral system is currently unavailable'];
            return back()->withNotify($notify);
        }

        $pageTitle = "Referral Logs";

        $logs = ReferralLog::where('user_id', auth()->id())->with('referee')->orderBy('id', 'desc')->paginate(getPaginate());

        return view('Template::user.referral.log', compact('pageTitle', 'logs'));
    }
}
