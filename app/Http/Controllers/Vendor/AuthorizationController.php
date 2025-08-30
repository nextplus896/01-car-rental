<?php

namespace App\Http\Controllers\Vendor;

use App\Constants\GlobalConst;
use App\Constants\SiteSectionConst;
use App\Http\Controllers\Controller;
use App\Models\Admin\BasicSettings;
use App\Models\Admin\SetupKyc;
use App\Models\Admin\SiteSections;
use App\Models\UserAuthorization;
use App\Models\UserKycData;
use App\Models\Vendor\VendorAuthorization;
use App\Providers\Admin\BasicSettingsProvider;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ControlDynamicInputFields;
use Illuminate\Support\Facades\DB;

class AuthorizationController extends Controller
{
    use ControlDynamicInputFields;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showMailFrom($token)
    {
        $page_title = setPageTitle("Mail Authorization");
        $site_name = BasicSettings::first()->site_name;
        $footer_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer = SiteSections::GetData($footer_slug)->first();
        return view('vendor-end.auth.authorize.verify-mail', compact("page_title", "token","site_name", "footer"));
    }

    /**
     * Verify authorization code.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function mailVerify(Request $request, $token)
    {
        $request->merge(['token' => $token]);
        $request->validate([
            'token'     => "required|string|exists:vendor_authorizations,token",
            'code'      => "required",
        ]);

        $code = implode('', $request->input('code'));
        $otp_exp_sec = BasicSettingsProvider::get()->otp_exp_seconds ?? GlobalConst::DEFAULT_TOKEN_EXP_SEC;
        $auth_column = VendorAuthorization::where("token", $request->token)->where("code", $code)->first();

        if (!$auth_column) {
            return redirect()->back()->with(['error' => [__('Invalid otp code')]]);
        }
        if ($auth_column->created_at->addSeconds($otp_exp_sec) < now()) {
            $this->authLogout($request);
            return redirect()->route('vendor.login')->with(['error' => ['Session expired. Please try again']]);
        }
        try {
            $auth_column->user->update([
                'email_verified'    => true,
            ]);
            $auth_column->delete();
        } catch (Exception $e) {
            $this->authLogout($request);
            return redirect()->route('vendor.login')->with(['error' => ['Something went wrong! Please try again']]);
        }

        return redirect()->intended(route("vendor.dashboard.index"))->with(['success' => ['Account successfully verified']]);
    }

    public function authLogout(Request $request)
    {
        auth()->guard("web")->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    public function showGoogle2FAForm()
    {
        $page_title =  __("Authorize Google Two Factor");
        $site_name = BasicSettings::first()?->site_name;
        $footer_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer = SiteSections::GetData($footer_slug)->first();
        return view('vendor-end.auth.authorize.verify-google-2fa', compact('page_title','site_name','footer'));
    }

    public function google2FASubmit(Request $request)
    {

        $request->validate([
            'code'    => "required",
        ]);

        $code = implode('', $request->input('code'));

        $user = auth()->guard('vendor')->user();

        if (!$user->two_factor_secret) {
            return back()->with(['warning' => ['Your secret key not stored properly. Please contact with system administrator']]);
        }

        if (google_2fa_verify($user->two_factor_secret, $code)) {

            $user->update([
                'two_factor_verified'   => true,
            ]);

            return redirect()->intended(route('vendor.dashboard.index'));
        }

        return back()->with(['warning' => ['Failed to login. Please try again']]);
    }
}
