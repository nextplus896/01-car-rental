<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Constants\GlobalConst;
use App\Constants\SiteSectionConst;
use App\Http\Controllers\Controller;
use App\Models\Admin\Currency;
use App\Models\Admin\SiteSections;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\UserWallet;
use App\Models\Vendor\Vendor;
use App\Providers\Admin\BasicSettingsProvider;
use App\Traits\User\LoggedInUsers;
use App\Traits\Vendor\LoggedInVendors;
use Exception;

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

    protected $request_data;
    protected $lockoutTime = 1;

    use AuthenticatesUsers, LoggedInVendors;

    public function showLoginForm(BasicSettingsProvider $basic_settings) {
        $site_name = $basic_settings->get()?->site_name;
        $basic_setting = $basic_settings->get();
        $page_title = setPageTitle(__("Vendor Login"));
        $client_ip = request()->ip() ?? false;
        $user_country = geoip()->getLocation($client_ip)['country'] ?? "";
        $auth_slug = Str::slug(SiteSectionConst::VENDOR_AUTH_SECTION);
        $auth = SiteSections::getData($auth_slug)->first();
        $footer_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer = SiteSections::getData($footer_slug)->first();
        return view('vendor-end.auth.auth',compact(
            'site_name',
            'page_title',
            'basic_setting',
            'user_country',
            'auth',
            'footer',
        ));
    }


    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $this->request_data = $request;
        $request->validate([
            'credentials'   => 'required|string',
            'password'      => 'required|string',
        ]);

        // if user exists with banner
        if(Vendor::where($this->username(),$request->credentials)->where('status',GlobalConst::BANNED)->exists()) {
            throw ValidationException::withMessages([
                'credentials'   => __('Your account has been suspended!'),
            ]);
        }

    }


    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $request->merge(['status' => true]);
        $request->merge([$this->username() => $request->credentials]);
        return $request->only($this->username(), 'password','status');
    }


    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        $request = $this->request_data->all();
        $credentials = $request['credentials'];
        if(filter_var($credentials,FILTER_VALIDATE_EMAIL)) {
            return "email";
        }
        return "username";
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            "credentials" => [trans('auth.failed')],
        ]);
    }


    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard("vendor");
    }


    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {

        $user->update([
            'two_factor_verified'   => false,
        ]);

        $this->refreshUserWallets($user);
        $this->createLoginLog($user);
        return redirect()->intended(route('vendor.dashboard.index'));
    }
}

