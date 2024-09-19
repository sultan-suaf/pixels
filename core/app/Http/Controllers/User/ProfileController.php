<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function profile()
    {
        $pageTitle = "Profile Setting";
        $user = auth()->user();
        return view('Template::user.profile_setting', compact('pageTitle','user'));
    }

    public function submitProfile(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
        ],[
            'firstname.required'=>'The first name field is required',
            'lastname.required'=>'The last name field is required'
        ]);

        $user = auth()->user();

        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;

        $user->address = $request->address;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->zip = $request->zip;

        $user->save();
        $notify[] = ['success', 'Profile updated successfully'];
        return back()->withNotify($notify);
    }

    public function changePassword()
    {
        $pageTitle = 'Change Password';
        return view('Template::user.password', compact('pageTitle'));
    }

    public function submitPassword(Request $request)
    {

        $passwordValidation = Password::min(6);
        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $request->validate([
            'current_password' => 'required',
            'password' => ['required','confirmed',$passwordValidation]
        ]);

        $user = auth()->user();
        if (Hash::check($request->current_password, $user->password)) {
            $password = Hash::make($request->password);
            $user->password = $password;
            $user->save();
            $notify[] = ['success', 'Password changed successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'The password doesn\'t match!'];
            return back()->withNotify($notify);
        }
    }

    public function updateProfilePicture(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' =>  ['required', new FileTypeValidate(['jpg', 'jpeg', 'png'])]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->all()
            ]);
        }

        $user = auth()->user();

        if ($request->hasFile('image')) {
            try {
                $path = getFilePath('userProfile');
                $size = getFileSize('userProfile');
                $user->image = fileUploader($request->image, $path, $size, $user->image);
            } catch (\Exception $exp) {
                return response()->json([
                    'status' => false,
                    'error' => 'Couldn\'t upload profile picture'
                ]);
            }
        }
        $user->save();

        return response()->json([
            'status' => true,
            'success' => 'Profile picture updated successfully',
            'image' => getImage(getFilePath('userProfile') . '/' . $user->image)
        ]);
    }


    
    public function updateCoverPhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cover_photo' =>  ['required', new FileTypeValidate(['jpg', 'jpeg', 'png'])]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->all()
            ]);
        }

        $user = auth()->user();

        if ($request->hasFile('cover_photo')) {
            try {
                $path = getFilePath('userProfile');
                $user->cover_photo = fileUploader($request->cover_photo, $path, null, $user->cover_photo);
            } catch (\Exception $exp) {
                return response()->json([
                    'status' => false,
                    'error' => 'Couldn\'t upload the cover photo'
                ]);
            }
        }
        $user->save();

        return response()->json([
            'status' => true,
            'success' => 'Cover photo updated successfully',
            'cover_photo' => getImage(getFilePath('userProfile') . '/' . $user->cover_photo)
        ]);
    }
    
    
}
