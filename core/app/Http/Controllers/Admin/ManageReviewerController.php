<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Reviewer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ManageReviewerController extends Controller
{
    public function all()
    {
        $pageTitle = 'All Reviewers';
        $reviewers = Reviewer::orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.reviewers', compact('pageTitle', 'reviewers'));
    }

    public function updateStatus($id)
    {
        $reviewer         = Reviewer::findOrFail($id);
        $reviewer->status = $reviewer->status ? 0 : 1;
        $reviewer->save();

        $notification = 'Reviewer banned successfully';
        if ($reviewer->status) {
            $notification = 'Reviewer unbanned Successfully';
        }

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function save(Request $request, $id = 0)
    {
        $passwordValidation = 'required';
        if ($id) {
            $passwordValidation = 'nullable';
        }
        $request->validate([
            'name'     => 'required|string|max:40',
            'username' => 'required|string|max:40|unique:reviewers,username,' . $id,
            'email'    => 'required|email|unique:reviewers,email,' . $id,
            'password' => $passwordValidation
        ]);
        
        $reviewer     = new Reviewer();
        $notification = 'Reviewer added successfully';
        
        if ($id) {
            $reviewer     = Reviewer::findOrFail($id);
            $notification = 'Reviewer updated successfully';
            
            if ($request->password) {
                notify($reviewer, 'REVIEWER_PASSWORD_UPDATE', [
                    'time' => showDateTime(now(), 'd M, Y h:i A')
                ]);
                $reviewer->password = Hash::make($request->password);
            }
        } else {
            notify($reviewer, 'REVIEWER_CREATED', [
                'time' => showDateTime(now(), 'd M, Y h:i A')
            ],['email','sms']);
            $reviewer->status = 1;
        }
     
        $reviewer->name     = $request->name;
        $reviewer->email    = $request->email;
        $reviewer->username = $request->username;
        $reviewer->save();


        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function login($id)
    {
        $reviewer = Reviewer::where('status', 1)->findOrFail($id);
        Auth::guard('reviewer')->loginUsingId($reviewer->id);
        return to_route('reviewer.dashboard');
    }
}
