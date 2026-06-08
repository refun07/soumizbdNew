<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;

class PasswordResetController extends Controller
{
    # set new password
    public function reset(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'status' => 200,
                'message' => translate('No user found with this email address.'),
                'authStatus' => 'user_not_found'
            ], 200);
        }
        if ($user->verification_code != $request->code) {
            return response()->json([
                'success' => false,
                'status' => 401,
                'message' => translate('Code does not match.'),
                'authStatus' => 'code_mismatch'
            ], 401);
        } else {

            if ($request->password != $request->passwordConfirmation) {
                return response()->json([
                    'success'       => false,
                    'status'        => 404,
                    'message'       => translate('Password confirmation does not match'),
                    'result'        => null
                ], 404);
            }

            $user->update([
                'password' => Hash::make($request->password),
            ]);
            $user->save();

            return response()->json([
                'success'   => true,
                'status'    => 200,
                'message'   => translate('Your password has been updated.')
            ], 200);
        }
    }
}
