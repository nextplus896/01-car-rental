<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Constants\SiteSectionConst;
use App\Http\Controllers\Controller;
use App\Models\Admin\SiteSections;
use App\Models\Vendor\Vendor;
use App\Providers\Admin\BasicSettingsProvider;
use App\Traits\User\RegisteredUsers;
use App\Traits\Vendor\LoggedInVendors;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers, RegisteredUsers, LoggedInVendors;

    protected $basic_settings;

    public function __construct()
    {
        $this->basic_settings = BasicSettingsProvider::get();
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm() {
        $site_name = $this->basic_settings->site_name;
        $basic_setting = $this->basic_settings;
        $client_ip = request()->ip() ?? false;
        $user_country = geoip()->getLocation($client_ip)['country'] ?? "";
        $auth_slug = Str::slug(SiteSectionConst::VENDOR_AUTH_SECTION);
        $auth = SiteSections::getData($auth_slug)->first();
        $footer_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer = SiteSections::getData($footer_slug)->first();
        $page_title = setPageTitle(__("Vendor Registration"));
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
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validated = $this->validator($request->all())->validate();
        $basic_settings             = $this->basic_settings;
        $validated['email_verified']    = ($basic_settings->vendor_email_verification == true) ? false : true;
        $validated['sms_verified']      = ($basic_settings->vendor_sms_verification == true) ? false : true;
        $validated['kyc_verified']      = ($basic_settings->vendor_kyc_verification == true) ? false : true;
        $validated['password']          = Hash::make($validated['password']);
        $validated['username']          = make_vendor_username(Str::slug($validated['firstname']),Str::slug($validated['lastname']));
        // $validated['referral_id']       = generate_unique_string('users','referral_id',8,'number');

        event(new Registered($user = $this->create($validated)));
        auth()->guard('vendor')->login($user);

        return $this->registered($request, $user);
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(array $data) {

        $basic_settings = $this->basic_settings;
        $password_rule = "required|string|min:6";
        if($basic_settings->vendor_secure_password) {
            $password_rule = ["required",Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised()];
        }

        if($basic_settings->vendor_agree_policy){
            $agree = 'required|in:on';
        }else{
            $agree = 'nullable';
        }


        return Validator::make($data,[
            'firstname'     => 'required|string|max:60',
            'lastname'      => 'required|string|max:60',
            'email'         => 'required|string|email|max:150|unique:vendors,email',
            'refer'         => 'sometimes|nullable|string|exists:vendors,referral_id',
            'password'      => $password_rule,
            'agree'         => $agree,
        ]);
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return Vendor::create($data);
    }


    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        try{
            $this->createUserWallets($user);
            $this->createLoginLog($user);
        }catch(Exception $e) {
            $this->guard()->logout();
            $user->delete();
            return redirect()->back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }
        return redirect()->intended(route('vendor.dashboard.index'));
    }
}
