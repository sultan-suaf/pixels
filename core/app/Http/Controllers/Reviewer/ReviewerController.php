<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ReviewerController extends Controller
{
    public function dashboard()
    {
        $reviewer                 = auth('reviewer')->user();
        $pageTitle                = 'Dashboard';
        $image                    = Image::query();
        $widget['total']          = (clone $image)->count();
        $widget['pending']        = (clone $image)->pending()->count();
        $widget['approved_by_me'] = (clone $image)->where('reviewer_id', $reviewer->id)->approved()->count();
        $widget['rejected_by_me'] = (clone $image)->where('reviewer_id', $reviewer->id)->rejected()->count();
        //chart data
        $report['months']   = collect([]);
        $report['approved'] = collect([]);
        $report['rejected'] = collect([]);

        $approvedImage = Image::where('reviewer_id', $reviewer->id)->approved()->whereYear('created_at', '>=', Carbon::now()->subYear())
            ->selectRaw("COUNT( CASE WHEN reviewer_id = $reviewer->id THEN id END) as total")
            ->selectRaw("DATE_FORMAT(created_at,'%M-%Y') as months")
            ->orderBy('created_at')
            ->groupBy(DB::Raw("MONTH(created_at)"))->get();


        $approvedImage->map(function ($item) use ($report) {
            $report['months']->push($item->months);
            $report['approved']->push($item->total);
        });


        $rejectedImage = Image::where('reviewer_id', $reviewer->id)->rejected()->whereYear('created_at', '>=', Carbon::now()->subYear())
            ->selectRaw("COUNT( CASE WHEN reviewer_id = $reviewer->id THEN id END) as total")
            ->selectRaw("DATE_FORMAT(created_at,'%M-%Y') as months")
            ->orderBy('created_at')
            ->groupBy(DB::Raw("MONTH(created_at)"))->get();

        $rejectedImage->map(function ($item) use ($report) {
            $report['months']->push($item->months);
            $report['rejected']->push($item->total);
        });

        $months = $report['months']->unique();

        for ($i = 0; $i < $months->count(); ++$i) {
            $monthVal = Carbon::parse($months[$i]);
            if (isset($months[$i + 1])) {
                $monthValNext = Carbon::parse($months[$i + 1]);
                if ($monthValNext < $monthVal) {
                    $temp           = $months[$i];
                    $months[$i]     = Carbon::parse($months[$i + 1])->format('F-Y');
                    $months[$i + 1] = Carbon::parse($temp)->format('F-Y');
                } else {
                    $months[$i] = Carbon::parse($months[$i])->format('F-Y');
                }
            }
        }
        return view('reviewer.dashboard', compact('pageTitle', 'widget', 'months', 'report'));
    }

    public function profile()
    {
        $pageTitle = 'Profile';
        $reviewer  = auth('reviewer')->user();
        return view('reviewer.profile', compact('pageTitle', 'reviewer'));
    }

    public function profileUpdate(Request $request)
    {

        $request->validate([
            'name'  => 'required',
            'email' => 'required|email',
            'image' => ['nullable', new FileTypeValidate(['jpg', 'jpeg', 'png'])]
        ]);

        
        $user = auth('reviewer')->user();

        if ($request->hasFile('image')) {
            try {
                $old         = $user->image;
                $user->image = fileUploader($request->image, getFilePath('reviewerProfile'), getFileSize('reviewerProfile'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->save();
        $notify[] = ['success', 'Profile updated successfully'];
        return to_route('reviewer.profile')->withNotify($notify);
    }

    public function password()
    {
        $pageTitle = 'Password Setting';
        $reviewer  = auth('reviewer')->user();
        return view('reviewer.password', compact('pageTitle', 'reviewer'));
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate($request, [
            'old_password' => 'required',
            'password'     => 'required|min:5|confirmed',
        ]);

        $user = auth('reviewer')->user();
        if (!Hash::check($request->old_password, $user->password)) {
            $notify[] = ['error', 'Password doesn\'t match!!'];
            return back()->withNotify($notify);
        }
        $user->password = bcrypt($request->password);
        $user->save();
        $notify[] = ['success', 'Password changed successfully.'];
        return to_route('reviewer.password')->withNotify($notify);
    }
}
