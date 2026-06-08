<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider(Request $request, $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback(Request $request, $provider)
    {
        try {
            if ($provider == 'twitter') {
                $user = Socialite::driver('twitter')->user();
            } else {
                $user = Socialite::driver($provider)->stateless()->user();
            }
        } catch (\Exception $e) {
            return redirect(config('app.url') . "?socialLogin=failed");
        }

        // check if they're an existing user
        $existingUser = User::where('provider_id', $user->id)->orWhere('email', $user->email)->first();

        if (!$existingUser) {
            // create a new user
            $newUser                    = new User;
            $newUser->name              = $user->name;
            $newUser->email             = $user->email;
            $newUser->email_verified_at = date('Y-m-d H:m:s');
            $newUser->provider_id       = $user->id;
            $newUser->save();
        } elseif ($existingUser && $existingUser->deleted_at != null) {
            return redirect(config('app.url') . "?socialLogin=failed");
        }

        $tokenResult = $existingUser ? $existingUser->createToken('Personal Access Token')->plainTextToken : $newUser->createToken('Personal Access Token')->plainTextToken;

        return redirect(config('app.url') . "?accessToken=" . $tokenResult);
    }

    # login credentials
    protected function credentials(Request $request)
    {
        if (filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)) {
            return $request->only($this->username(), 'password');
        }
        return ['phone' => $request->get('email'), 'password' => $request->get('password')];
    }

    # redirect after authentication
    public function authenticated()
    {
        if (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff') {
            return redirect()->route('admin.dashboard');
        } else if (auth()->user()->user_type == 'seller') {
            if (config('app.app_mode') == "singleVendor") {
                flash(translate('Seller panel is not available right now.'))->error();
                $this->guard()->logout();
            }
            return redirect()->route('seller.dashboard');
        } else {
            $this->guard()->logout();
            flash(translate('Customers can not login from here'))->error();
            return redirect()->route('login');
        }
    }

    # login failed response
    protected function sendFailedLoginResponse(Request $request)
    {
        flash(translate('Invalid email or password'))->error();
        return back();
    }

    # logout
    public function logout(Request $request)
    {
        if (auth()->user() != null && (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff' || auth()->user()->user_type == 'seller')) {
            $redirect_route = 'login';
        } else {
            $redirect_route = 'home';
        }

        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect()->route($redirect_route);
    }
}
