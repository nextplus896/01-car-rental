<?php

namespace App\Http\Controllers\Api\V1\Vendor\Auth;

use Exception;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Http\Helpers\Response;
use App\Models\Admin\SetupKyc;
use Illuminate\Support\Carbon;
use App\Models\UserAuthorization;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Vendor\VendorAuthorization;
use App\Notifications\Vendor\Auth\SendAuthorizationCode;
use Illuminate\Support\Facades\Auth;
use App\Traits\ControlDynamicInputFields;
use Illuminate\Support\Facades\Validator;
use App\Providers\Admin\BasicSettingsProvider;


class AuthorizationController extends Controller
{
    use ControlDynamicInputFields;

    public static function sendCodeToMail($user = null) {

        if(!$user && auth()->guard("vendor_api")->check() == false) throw new Exception(__("Access denied! Unauthenticated"));
        if(!$user) $user = auth()->guard("vendor_api")->user();

        $data = [
            'user_id'       => $user->id,
            'code'          => generate_random_code(),
            'token'         => generate_unique_string("vendor_authorizations","token",200),
            'created_at'    => now(),
        ];

        DB::beginTransaction();
        try{
            VendorAuthorization::where("user_id",$user->id)->delete();
            DB::table("vendor_authorizations")->insert($data);
            $user->notify(new SendAuthorizationCode((object) $data));
            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            throw new Exception(__("Something went wrong! Please try again"));
        }

        return $data;
    }

    public function resendCodeToMail(Request $request) {
        $validator = Validator::make($request->all(),[
            'token'     => "required|string|exists:vendor_authorizations,token"
        ]);
        if($validator->fails()) return Response::error($validator->errors()->all(),[]);
        $validated = $validator->validate();
        $user_authorize = VendorAuthorization::where("token",$validated['token'])->first();

        if(!$user_authorize) return Response::error([__("Request token is invalid")],[],404);

        if(Carbon::now() <= $user_authorize->created_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE)) {
            return Response::error([__('You can resend verification code after ').Carbon::now()->diffInSeconds($user_authorize->created_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE)). __(' seconds')],['token' => $validated['token'], 'wait_time' => (string) Carbon::now()->diffInSeconds($user_authorize->created_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE))],400);
        }

        $resend_code = generate_random_code();
        try{
            $user_authorize->update([
                'code'          => $resend_code,
                'created_at'    => now(),
            ]);
            $data = $user_authorize->toArray();
            $user_authorize->user->notify(new SendAuthorizationCode((object) $data));
        }catch(Exception $e) {
            return Response::error([__("Something went wrong! Please try again")],[],500);
        }

        return Response::success([__("Verification code resend successfully!")],['token' => $validated['token'],'wait_time' => ""],200);
    }

    public function verifyMailCode(Request $request) {
        $validator = Validator::make($request->all(),[
            'token'     => "required|string|exists:vendor_authorizations,token",
            'code'      => "required|integer",
        ]);
        if($validator->fails()) {
            return Response::error($validator->errors()->all(),[],400);
        }
        $validated = $validator->validate();

        if(!VendorAuthorization::where("code",$request->code)->exists()) {
            return Response::error([__("Invalid OTP. Please try again")],[],404);
        }

        $otp_exp_sec = BasicSettingsProvider::get()->otp_exp_seconds ?? GlobalConst::DEFAULT_TOKEN_EXP_SEC;
        $auth_column = VendorAuthorization::where("token",$request->token)->where("code",$request->code)->first();
        if($auth_column->created_at->addSeconds($otp_exp_sec) < now()) {
            $auth_column->delete();
            $this->authLogout($request);
            return Response::error([__("Session expired. Please try again")],[],440);
        }

        try{
            $auth_column->user->update([
                'email_verified'    => true,
            ]);
            $auth_column->delete();
        }catch(Exception $e) {
            $auth_column->delete();
            $this->authLogout($request);
            return Response::error([__("Something went wrong! Please try again")],[],500);
        }

        return Response::success([__("Account successfully verified")],[],200);
    }

    public function authLogout(Request $request) {
        $user_token = Auth::guard(get_auth_guard())->user()->token();
        $user_token->revoke();
    }

    // Get KYC Input Fields
    public function getKycInputFields() {

        $user = auth()->guard('vendor_api')->user();

        $instructions = "0: Default, 1: Approved, 2: Pending, 3:Rejected";

        if($user->kyc_verified == GlobalConst::VERIFIED) return Response::success([__("You are already KYC Verified User")],[
            'instructions'  => $instructions,
            'status'        => $user->kyc_verified,
            'input_fields'  => [],
        ],200);
        if($user->kyc_verified == GlobalConst::PENDING) return Response::success([__('Your KYC information is submitted. Please wait for admin confirmation')],[
            'instructions'  => $instructions,
            'status'        => $user->kyc_verified,
            'input_fields'  => [],
        ],200);
        $user_kyc = SetupKyc::vendorKyc()->first();
        if(!$user_kyc) return Response::error([__('User KYC section is under maintenance')],[],503);
        $kyc_data = $user_kyc->fields;
        if(!$kyc_data) return Response::error([__('User KYC section is under maintenance')],[],503);
        $kyc_fields = array_reverse($kyc_data);
        return Response::success([__('User KYC input fields fetch successfully!')],['instructions'  => $instructions,'status' => $user->kyc_verified,'input_fields' => $kyc_fields],200);
    }

    public function KycSubmit(Request $request) {
        $user = auth()->guard('vendor_api')->user();
        if($user->kyc_verified == GlobalConst::VERIFIED) return Response::warning([__('You are already KYC Verified User')],[],400);

        $user_kyc_fields = SetupKyc::vendorKyc()->first()->fields ?? [];
        $validation_rules = $this->generateValidationRules($user_kyc_fields);

        $validated = Validator::make($request->all(),$validation_rules)->validate();
        $get_values = $this->placeValueWithFields($user_kyc_fields,$validated);

        $create = [
            'user_id'       => auth()->guard('vendor_api')->user()->id,
            'data'          => json_encode($get_values),
            'created_at'    => now(),
        ];

        DB::beginTransaction();
        try{
            DB::table('vendor_kyc_data')->updateOrInsert(["user_id" => $user->id],$create);
            $user->update([
                'kyc_verified'  => GlobalConst::PENDING,
            ]);
            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            $user->update([
                'kyc_verified'  => GlobalConst::DEFAULT,
            ]);
            $this->generatedFieldsFilesDelete($get_values);
            return Response::error([__('Something went wrong! Please try again')],[],500);
        }

        return Response::success([__('KYC information successfully submitted')],[],200);
    }

    public function get2FaStatus()
    {
        $user = auth()->guard('vendor_api')->user();
        $qr_code = generate_google_2fa_auth_qr();
        $qr_secret = $user->two_factor_secret;
        $message = __("Your account secure with google 2FA");
        if ($user->two_factor_status == false) $message = __("To enable two factor authentication (powered by google) please visit your web dashboard Click here:") . " " . setRoute("vendor.security.google.2fa");

        return Response::success([__('Request response fetch successfully!')], [
            'qr_code'    => $qr_code,
            'qr_secret' => $qr_secret,
            'status' => $user->two_factor_status,
            'message'   => $message,
        ], 200);
    }
    public function google2FAStatusUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status'        => "required|numeric",
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->all(), [], 400);
        }

        $validated = $validator->validated();

        $user = Auth::guard("vendor_api")->user();


        try {
            $user->update([
                'two_factor_status'         => $validated['status'],
                'two_factor_verified'       => true,
            ]);
        } catch (Exception $e) {
            return Response::error([__('Something went wrong! Please try again')], [], 500);
        }
        return Response::success([__('Google 2FA Updated Successfully!')], [], 200);
    }
    public function verifyGoogle2Fa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code'      => "required|integer",
        ]);
        if ($validator->fails()) return Response::error($validator->errors()->all(), []);
        $validated = $validator->validate();

        $code = $validated['code'];

        $user = auth()->guard("vendor_api")->user();

        if (!$user->two_factor_secret) {
            return Response::error([__('Your secret key not stored properly. Please contact with system administrator')], [], 400);
        }

        if (google_2fa_verify($user->two_factor_secret, $code)) {

            $user->update([
                'two_factor_verified'   => true,
            ]);

            return Response::success([__('Google 2FA successfully verified!')], [], 200);
        } else if (google_2fa_verify($user->two_factor_secret, $code) === false) {
            return Response::error([__('Invalid authentication code')], [], 400);
        }

        return Response::error([__('Failed to login. Please try again')], [], 500);
    }
}
