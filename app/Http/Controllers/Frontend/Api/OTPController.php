<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\SmsService;
use App\Models\OrderOtp;
use Illuminate\Support\Facades\Cache;

class OTPController extends Controller
{
    protected $sms;

    public function __construct(SmsService $sms)
    {
        $this->sms = $sms;
    }
    public function sendOTP(Request $request){
        $to = $request->input('phone');

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);

        // Store in DB
        OrderOtp::updateOrCreate(
            ['phone' => $to],
            [
                'otp' => $otp,
                'expires_at' => now()->addMinutes(15)
            ]
        );
        try{
            if($this->sms){
                $message= $request->name." Your Verification OTP For ".env('APP_NAME'). " is ". $otp." Expires in 15 minutes";
                $res=$this->sms->send(normalizeBangladeshiNumber($to), $message);
                if($res['response_code'] == 202){
                    return response()->json(
                                    [
                            'success'   => true,
                            'status'    => 200,
                            'message'   => 'OTP sent successfully',
                            'result'    => null
                        ],
                        200
                    );
                }else{
                    return response()->json(
                                    [
                            'success'   => false,
                            'status'    => 200,
                            'message'   => $res['error_message'],
                            'result'    => null
                        ],
                        200
                    );
                }

            }

        }catch (\Exception $e) {
            dd($e);
        }
    }
    public function verifyOtpForGuest(Request $request)
    {
        $phone = $request->input('phone');
        $otp = $request->input('otp');

        $record = OrderOtp::where('phone', $phone)
            ->where('otp', $otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return response()->json(['success'=>false,'mesage' => 'Invalid or expired OTP'], 200);
        }

        // OTP is valid
        // You can optionally delete the OTP now
        $record->delete();

        return response()->json(['success'=>true,'message' => 'OTP verified'],200);
    }


    public function resendOtp(Request $request)
    {
        $phone = $request->input('phone');

        // Optional: throttle OTP requests to once every 60 seconds per phone number
        $cacheKey = 'otp_resend_' . $phone;
        if (Cache::has($cacheKey)) {
            return response()->json([
                'success'=>false,
                'message' => 'Please wait before requesting another OTP.'
            ], 200); // Too Many Requests
        }

        // Generate new OTP
        $otp = rand(100000, 999999);

        // Update or create new OTP record
        OrderOtp::updateOrCreate(
            ['phone' => $phone],
            [
                'otp' => $otp,
                'expires_at' => now()->addMinutes(15)
            ]
        );

        // Send OTP via SMS
        $this->sms->send($phone, "Your new OTP is: $otp");

        // Set cooldown timer (e.g., 60 seconds)
        Cache::put($cacheKey, true, 60);

        return response()->json(['success'=>true,'message' => 'A new OTP has been sent to your phone.']);
    }


}
