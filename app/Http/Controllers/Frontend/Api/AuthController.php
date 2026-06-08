<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Models\Cart;
use App\Models\MediaFile;
use App\Models\Notification;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\User;
use App\Models\OrderOtp;
use App\Notifications\CustomerRegistration;
use App\Notifications\EmailVerification;
use App\Services\SmsService;

class AuthController extends Controller
{
    protected $sms;

    public function __construct(SmsService $sms)
    {
        $this->sms = $sms;
    }

    # new sign up
    public function signup(Request $request)
    {
        $isPhoneLogin = getSetting('customerLoginMethod') === 'phone';
        
        // Normalize phone number to local format (01XXXXXXXXX) for DB storage
        $phone = $request->phone ? normalizePhoneToLocal($request->phone) : null;
        
        // Check for existing phone
        if ($phone && User::where('phone', $phone)->first() != null) {
            return response()->json([
                'success' => false,
                'message' => translate('User already exists with this phone number'),
                'data' => null,
                'authStatus' => 'user_exists_phone'
            ], 409);
        }

        // Check for existing email (only if email is provided)
        if ($request->email && User::where('email', $request->email)->first() != null) {
            return response()->json([
                'success' => false,
                'message' => translate('User already exists with this email'),
                'data' => null,
                'authStatus' => 'user_exists_email'
            ], 409);
        }

        $user = new User([
            'name' => $request->name,
            'email' => $request->email ?: null,
            'phone' => $phone,
            'password' => Hash::make($request->password),
            'verification_code' => rand(100000, 999999)
        ]);

        // For phone login mode, send SMS OTP if OTP is enabled
        if ($isPhoneLogin) {
            // Check if OTP is enabled
            if (config('app.OTP_ACTIVATION') == '0') {
                // OTP disabled - auto-verify user
                $user->email_or_otp_verified = 1;
                $user->save();
                
                if ($request->has('guestUserId') && $request->guestUserId != null) {
                    Cart::where('guest_user_id', $request->guestUserId)->update(
                        [
                            'user_id' => $user->id,
                            'guest_user_id' => null
                        ]
                    );
                }
                
                // Send registration notification
                try {
                    $user->notify(new CustomerRegistration());
                } catch (\Throwable $th) {
                    // Silent fail
                }
                
                // Create notification for admin
                Notification::create([
                    'shop_id'   => 1,
                    'for'       => 'admin',
                    'type'      => 'customer-registration',
                    'text'      => 'Customer Registration',
                    'link_info' => $user->phone,
                ]);
                
                $tokenResult = $user->createToken('Personal Access Token')->plainTextToken;
                return $this->loginSuccess($tokenResult, $user);
            }
            
            // OTP enabled - proceed with SMS verification
            // Generate 6-digit OTP
            $otp = rand(100000, 999999);
            
            // Store in DB
            OrderOtp::updateOrCreate(
                ['phone' => $phone],
                [
                    'otp' => $otp,
                    'expires_at' => now()->addMinutes(15)
                ]
            );
            
            // Send SMS
            try {
                if ($this->sms) {
                    $message = $request->name . " Your Verification OTP For " . env('APP_NAME') . " is " . $otp . " Expires in 15 minutes";
                    $res = $this->sms->send(normalizeBangladeshiNumber($phone), $message);
                    if ($res && isset($res['response_code']) && $res['response_code'] == 202) {
                        $user->save();
                        
                        if ($request->has('guestUserId') && $request->guestUserId != null) {
                            Cart::where('guest_user_id', $request->guestUserId)->update(
                                [
                                    'user_id' => $user->id,
                                    'guest_user_id' => null
                                ]
                            );
                        }
                        
                        return response()->json([
                            'success' => true,
                            'message' => translate('A verification code has been sent to your phone'),
                            'authStatus' => 'verify',
                            'phone' => $phone,
                        ], 200);
                    } else {
                        Log::error('SMS sending failed during registration', [
                            'phone' => $phone,
                            'response' => $res,
                            'reason' => 'sms_gateway_error'
                        ]);
                        return response()->json([
                            'success' => false,
                            'message' => translate('Failed to send SMS. Please try again.'),
                            'authStatus' => 'sms_failed'
                        ], 500);
                    }
                }
            } catch (\Exception $e) {
                Log::error('SMS sending exception during registration', [
                    'phone' => $phone,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => translate('Failed to send SMS. Please try again.'),
                    'authStatus' => 'sms_error'
                ], 500);
            }
        } else {
            // Email login mode
            if (getSetting('emailVerification') != 1) {
                $user->email_or_otp_verified = 1;
                $user->email_verified_at     = date('Y-m-d H:m:s');
            } else {
                try {
                    $user->notify(new EmailVerification());
                } catch (\Exception $e) {
                    //dd($e->getMessage());
                }
            }
            $user->save();

            if ($request->has('guestUserId') && $request->guestUserId != null) {
                Cart::where('guest_user_id', $request->guestUserId)->update(
                    [
                        'user_id' => $user->id,
                        'guest_user_id' => null
                    ]
                );
            }

            if (getSetting('emailVerification') == 1) {
                return response()->json([
                    'success' => true,
                    'message' => translate('A verification code has been sent to your email'),
                    'authStatus' => 'verify'
                ], 200);
            }

            // email notification  
            try {
                $user->notify(new CustomerRegistration());
            } catch (\Throwable $th) {
                //throw $th;
            }

            // notification
            Notification::create([
                'shop_id'   => 1,
                'for'       => 'admin',
                'type'      => 'customer-registration',
                'text'      => 'Customer Registration',
                'link_info' => $user->email ? $user->email : $user->phone,
            ]);

            $tokenResult = $user->createToken('Personal Access Token')->plainTextToken;
            return $this->loginSuccess($tokenResult, $user);
        }
    }

    # login
    public function login(Request $request)
    {
        $isPhoneLogin = getSetting('customerLoginMethod') === 'phone';
        
        if ($isPhoneLogin) {
            $request->validate([
                'phone' => 'required|string',
                'password' => 'required|string',
            ]);
            
            // Normalize phone number to local format (01XXXXXXXXX)
            $phone = normalizePhoneToLocal($request->phone);

            // Find user by phone
            $user = User::where('phone', $phone)->first();

            if (!$user) {
                Log::warning('Login failed: User not found', [
                    'phone' => $phone,
                    'ip' => $request->ip(),
                    'reason' => 'user_not_found'
                ]);
                return response()->json([
                    'success' => false,
                    'message' => translate('Invalid login information'),
                    'authStatus' => 'invalid_credentials'
                ], 401);
            }

            if (!Hash::check($request->password, $user->password)) {
                Log::warning('Login failed: Invalid password', [
                    'phone' => $phone,
                    'user_id' => $user->id,
                    'ip' => $request->ip(),
                    'reason' => 'invalid_password'
                ]);
                return response()->json([
                    'success' => false,
                    'message' => translate('Invalid login information'),
                    'authStatus' => 'invalid_credentials'
                ], 401);
            }
            
            // Log the user in manually
            Auth::login($user);
        } else {
            $request->validate([
                'email' => 'required|string',
                'password' => 'required|string',
            ]);
            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                $existingUser = User::where('email', $request->email)->first();
                Log::warning('Login failed: Invalid credentials', [
                    'email' => $request->email,
                    'user_exists' => $existingUser ? true : false,
                    'user_id' => $existingUser ? $existingUser->id : null,
                    'ip' => $request->ip(),
                    'reason' => $existingUser ? 'invalid_password' : 'user_not_found'
                ]);
                return response()->json([
                    'success' => false,
                    'message' => translate('Invalid login information'),
                    'authStatus' => 'invalid_credentials'
                ], 401);
            }

            $user = $request->user();
        }

        if (!isset($user)) {
            $user = $request->user();
        }
        
        if ($user->banned == 1) {
            Log::warning('Login failed: User banned', [
                'user_id' => $user->id,
                'email' => $user->email,
                'phone' => $user->phone,
                'ip' => $request->ip(),
                'reason' => 'user_banned'
            ]);
            return response()->json([
                'success' => false,
                'message' => translate('You are banned & can not login'),
                'authStatus' => 'banned'
            ], 403);
        }
        if ($request->has('guestUserId') && $request->guestUserId != null) {
            Cart::where('guest_user_id', $request->guestUserId)->update(
                [
                    'user_id' => $user->id,
                    'guest_user_id' => null
                ]
            );
        }

        if ($user->user_type == 'customer') {
            // For phone login, check phone verification status (only if OTP is enabled)
            if ($isPhoneLogin && config('app.OTP_ACTIVATION') != '0' && $user->email_or_otp_verified == 0) {
                return response()->json([
                    'success' => true,
                    'verified' => false,
                    'message' => translate('Please verify your phone number'),
                    'authStatus' => 'verify',
                    'phone' => $user->phone,
                ], 200);
            }
            
            // For email login, check email verification
            if (!$isPhoneLogin && getSetting('emailVerification') == 1 && $user->email_verified_at == null) {
                try {
                    $user->notify(new EmailVerification());
                } catch (\Exception $e) {
                    //dd($e->getMessage());
                }
                return response()->json([
                    'success' => true,
                    'verified' => false,
                    'message' => translate('Please verify your account'),
                    'authStatus' => 'verify'

                ], 200);
            }
            $tokenResult = $user->createToken('Personal Access Token')->plainTextToken;
            return $this->loginSuccess($tokenResult, $user);
        } else {
            Log::warning('Login failed: Non-customer attempted login', [
                'user_id' => $user->id,
                'email' => $user->email,
                'phone' => $user->phone,
                'user_type' => $user->user_type,
                'ip' => $request->ip(),
                'reason' => 'not_customer'
            ]);
            return response()->json([
                'success' => false,
                'message' => translate('Only customers can login here'),
                'authStatus' => 'only_customer_login'
            ], 403);
        }
    }

    # verify account - email
    public function verify(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => translate('No user found with this email address'),
                'authStatus' => 'user_not_found'
            ], 404);
        }
        if ($user->verification_code != $request->code) {
            return response()->json([
                'success' => false,
                'message' => translate('Code does not match'),
                'authStatus' => 'code_mismatch'
            ], 401);
        } else {
            $user->email_verified_at = date('Y-m-d H:m:s');
            $user->save();
            $tokenResult = $user->createToken('Personal Access Token')->plainTextToken;
            return $this->loginSuccess($tokenResult, $user);
        }
    }

    # resend verification code
    public function resend_code(Request $request)
    {
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {

            $user = User::where('email', $request->email)->first();
            if ($user != null) {
                $user->verification_code = rand(100000, 999999);
                $user->save();

                try {
                    $user->notify(new EmailVerification());
                } catch (\Exception $e) {
                    //dd($e->getMessage());
                }

                return response()->json([
                    'success'       => true,
                    'status'        => 200,
                    'message'       => translate('A verification code has been sent to your email'),
                    'authStatus'    => 'verification_code_sent'
                ], 200);
            } else {
                return response()->json([
                    'success'       => false,
                    'status'        => 400,
                    'message'       => translate('No user found with this email address'),
                    'authStatus'    => 'user_not_found'
                ], 404);
            }
        } else {
            return response()->json([
                'success'       => false,
                'status'        => 400,
                'message'       => translate('Invalid email address'),
                'authStatus'    => 'invalid_email'
            ], 400);
        }
    }

    # verify code
    public function verifyCode(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => translate('No user found with this email address.'),
                'authStatus' => 'user_not_found'
            ], 404);
        }
        if ($user->verification_code != $request->code) {
            return response()->json([
                'success' => false,
                'status' => 401,
                'message' => translate('Code does not match.'),
                'authStatus' => 'code_mismatch'
            ], 401);
        } else {
            return response()->json([
                'success'   => true,
                'status'    => 200,
                'message'   => translate('Your code has been matched.')
            ], 200);
        }
    }

    # return logged in user
    public function user(Request $request)
    {
        try {
            $user = $request->user();
            return $this->loginSuccess('', $user);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'status' => 401,
                'message' => $th->getMessage(),
            ], 200);
        }
    }

    # logout
    public function logout(Request $request)
    {
        $user = auth('sanctum')->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    # login success response
    protected function loginSuccess($token, $user)
    {
        // carts
        $cartItems = $user->carts(session('WarehouseIds'));

        // user timeline 
        $count = 0;
        $unit  =  translate('Years');
        if (now()->diffInYears($user->created_at) == 0) {
            if (now()->diffInMonths($user->created_at) == 0) {
                $count = now()->diffInDays($user->created_at);
                $unit =  translate('Days');
            } else {
                $count = now()->diffInMonths($user->created_at);
                $unit =  translate('Months');
            }
        } else {
            $count = now()->diffInYears($user->created_at);
        }

        // total orders
        $totalOrders    = $user->orders()->count();
        $totalExpense   = $user->orders()->where('payment_status', 'paid')->whereNot('delivery_status', 'cancelled')->sum('total_amount');

        // total products
        $totalProducts = OrderItem::whereHas('order', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->sum('qty');

        return response()->json([
            'success'       => true,
            'access_token'  => $token,
            'token_type'    => 'Bearer',
            'expires_at'    => Carbon::parse(Carbon::now()->addWeeks(100))->toDateTimeString(),
            'user'          => new UserResource($user),
            'carts'         => CartResource::collection($cartItems),
            'dashboardInfo' => [
                'timeline'  => [
                    'count'     => $count < 10 ? '0' . $count : $count,
                    'unit'      => $unit
                ],
                'totalOrders'   => (float) $totalOrders,
                'totalExpense'  => (float) $totalExpense,
                'totalProducts' => (float) $totalProducts
            ],
            'message'       => 'Logged In'
        ]);
    }

    # update info
    public function updateInfo(Request $request)
    {
        $user           = apiUser();
        $user->name     = $request->name;
        $user->phone    = $request->phone;

        if ($request->hasFile('avatar')) {
            $type = array(
                "jpg" => "image",
                "jpeg" => "image",
                "png" => "image",
                "svg" => "image",
                "webp" => "image",
                "gif" => "image",
                "mp4" => "video",
                "mpg" => "video",
                "mpeg" => "video",
                "webm" => "video",
                "ogg" => "video",
                "avi" => "video",
                "mov" => "video",
                "flv" => "video",
                "swf" => "video",
                "mkv" => "video",
                "wmv" => "video",
                "wma" => "audio",
                "aac" => "audio",
                "wav" => "audio",
                "mp3" => "audio",
                "zip" => "archive",
                "rar" => "archive",
                "7z" => "archive",
                "doc" => "document",
                "txt" => "document",
                "docx" => "document",
                "pdf" => "document",
                "csv" => "document",
                "xml" => "document",
                "ods" => "document",
                "xlr" => "document",
                "xls" => "document",
                "xlsx" => "document"
            );

            $mediaFile = new MediaFile;
            $mediaFile->media_name = null;

            $arr = explode('.', $request->file('avatar')->getClientOriginalName());

            for ($i = 0; $i < count($arr) - 1; $i++) {
                if ($i == 0) {
                    $mediaFile->media_name .= $arr[$i];
                } else {
                    $mediaFile->media_name .= "." . $arr[$i];
                }
            }

            $mediaFile->media_file = $request->file('avatar')->store('uploads/all');
            $mediaFile->user_id = Auth::user()->id;
            $mediaFile->media_extension = $request->file('avatar')->getClientOriginalExtension();
            if (isset($type[$mediaFile->media_extension])) {
                $mediaFile->media_type = $type[$mediaFile->media_extension];
            } else {
                $mediaFile->media_type = "others";
            }
            $mediaFile->media_size = $request->file('avatar')->getSize();
            $mediaFile->save();

            $user->update([
                'avatar' => $mediaFile->id,
            ]);
        }

        $user->save();

        return response()->json([
            'success'       => true,
            'status'        => 200,
            'message'       => translate('Profile updated successfully'),
            'result'        => [
                'user'    => new UserResource($user),
            ]
        ], 200);
    }

    #  update password
    public function updatePassword(Request $request)
    {
        $user = apiUser();
        if ($request->password != $request->passwordConfirmation) {
            return response()->json([
                'success'       => false,
                'status'        => 404,
                'message'       => translate('Password confirmation does not match'),
                'result'        => null
            ], 404);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'success'       => true,
            'status'        => 200,
            'message'       => translate('Password updated successfully'),
            'result'        => null
        ], 200);
    }

    # Verify phone OTP for registration
    public function verifyPhoneOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string',
        ]);

        // Normalize phone number to local format (01XXXXXXXXX)
        $phone = normalizePhoneToLocal($request->phone);

        $record = OrderOtp::where('phone', $phone)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => translate('Invalid or expired OTP'),
                'authStatus' => 'invalid_otp'
            ], 400);
        }

        // Delete the OTP record
        $record->delete();

        // Find and update the user
        $user = User::where('phone', $phone)->first();
        if ($user) {
            $user->email_or_otp_verified = 1;
            $user->save();

            // Create notification
            Notification::create([
                'shop_id'   => 1,
                'for'       => 'admin',
                'type'      => 'customer-registration',
                'text'      => 'Customer Registration',
                'link_info' => $user->phone,
            ]);

            $tokenResult = $user->createToken('Personal Access Token')->plainTextToken;
            return $this->loginSuccess($tokenResult, $user);
        }

        return response()->json([
            'success' => false,
            'message' => translate('User not found'),
            'authStatus' => 'user_not_found'
        ], 404);
    }

    # Resend phone OTP
    public function resendPhoneOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        // Normalize phone number to local format (01XXXXXXXXX)
        $phone = normalizePhoneToLocal($request->phone);

        // Check if user exists
        $user = User::where('phone', $phone)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => translate('No user found with this phone number'),
                'authStatus' => 'user_not_found'
            ], 404);
        }

        // Generate new OTP
        $otp = rand(100000, 999999);

        // Store in DB
        OrderOtp::updateOrCreate(
            ['phone' => $phone],
            [
                'otp' => $otp,
                'expires_at' => now()->addMinutes(15)
            ]
        );

        // Send SMS
        try {
            if ($this->sms) {
                $message = $user->name . " Your Verification OTP For " . env('APP_NAME') . " is " . $otp . " Expires in 15 minutes";
                $res = $this->sms->send(normalizeBangladeshiNumber($phone), $message);
                if ($res && isset($res['response_code']) && $res['response_code'] == 202) {
                    return response()->json([
                        'success' => true,
                        'message' => translate('A new verification code has been sent to your phone'),
                        'authStatus' => 'otp_sent'
                    ], 200);
                } else {
                    Log::error('SMS sending failed during resend OTP', [
                        'phone' => $phone,
                        'user_id' => $user->id,
                        'response' => $res,
                        'reason' => 'sms_gateway_error'
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('SMS sending exception during resend OTP', [
                'phone' => $phone,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => translate('Failed to send SMS. Please try again.'),
            'authStatus' => 'sms_failed'
        ], 500);
    }

    # Send OTP for password reset via phone
    public function sendPasswordResetOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        // Normalize phone number to local format (01XXXXXXXXX)
        $phone = normalizePhoneToLocal($request->phone);

        // Check if user exists
        $user = User::where('phone', $phone)->where('user_type', 'customer')->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => translate('No user found with this phone number'),
                'authStatus' => 'user_not_found'
            ], 404);
        }

        // Generate OTP
        $otp = rand(100000, 999999);

        // Store in DB
        OrderOtp::updateOrCreate(
            ['phone' => $phone],
            [
                'otp' => $otp,
                'expires_at' => now()->addMinutes(15)
            ]
        );

        // Send SMS
        try {
            if ($this->sms) {
                $message = $user->name . " Your Password Reset OTP For " . env('APP_NAME') . " is " . $otp . " Expires in 15 minutes";
                $res = $this->sms->send(normalizeBangladeshiNumber($phone), $message);
                if ($res && isset($res['response_code']) && $res['response_code'] == 202) {
                    return response()->json([
                        'success' => true,
                        'message' => translate('A verification code has been sent to your phone'),
                        'authStatus' => 'otp_sent',
                        'phone' => $phone,
                    ], 200);
                } else {
                    Log::error('SMS sending failed during password reset', [
                        'phone' => $phone,
                        'user_id' => $user->id,
                        'response' => $res,
                        'reason' => 'sms_gateway_error'
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('SMS sending exception during password reset', [
                'phone' => $phone,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => translate('Failed to send SMS. Please try again.'),
            'authStatus' => 'sms_failed'
        ], 500);
    }

    # Reset password with phone OTP
    public function resetPasswordWithPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string',
            'password' => 'required|string|min:6',
            'passwordConfirmation' => 'required|string|same:password',
        ]);

        // Normalize phone number to local format (01XXXXXXXXX)
        $phone = normalizePhoneToLocal($request->phone);

        // Verify OTP
        $record = OrderOtp::where('phone', $phone)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => translate('Invalid or expired OTP'),
                'authStatus' => 'invalid_otp'
            ], 400);
        }

        // Delete the OTP record
        $record->delete();

        // Find and update the user
        $user = User::where('phone', $phone)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => translate('User not found'),
                'authStatus' => 'user_not_found'
            ], 404);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => translate('Password reset successfully. Please login with your new password.'),
            'authStatus' => 'password_reset_success'
        ], 200);
    }
}
