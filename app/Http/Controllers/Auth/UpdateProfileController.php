<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Hash;
use Illuminate\Http\Request;

class UpdateProfileController extends Controller
{
    # profile
    public function edit()
    {
        $user = user();
        return view('backend.profile.edit', compact('user'));
    }

    # update profile
    public function update(Request $request)
    {
        $user           = user();
        $user->name     = $request->name;
        $user->email    = $request->email;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->avatar   = $request->avatar;
        $user->save();

        flash(translate('Profile updated successfully'))->success();
        return back();
    }
}
