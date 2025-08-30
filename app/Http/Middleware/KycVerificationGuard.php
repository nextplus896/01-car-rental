<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Providers\Admin\BasicSettingsProvider;
use App\Constants\GlobalConst;
use App\Http\Helpers\Response;

class KycVerificationGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $basic_settings = BasicSettingsProvider::get();

        $guard = userGuard()['type'];

        if( $guard === 'USER'){
            $kyc_verification_status = $basic_settings->kyc_verification;
        }elseif($guard === 'VENDOR'){
            $kyc_verification_status = $basic_settings->vendor_kyc_verification;
        }
        if($kyc_verification_status) {
            $user = auth()->user();
            if($user->kyc_verified != GlobalConst::APPROVED) {

                $smg = __("Please verify your KYC information first");
                if($user->kyc_verified == GlobalConst::PENDING) {
                    $smg = __("Your KYC information is pending. Please wait for admin confirmation.");
                }
                if(request()->expectsJson()) {
                    return Response::error([$smg],[],400);
                }
                if(auth()->guard("web")->check()) {
                    return redirect()->route("user.kyc.index")->with(['warning' => [$smg]]);
                }else if(auth()->guard("vendor")->check()) {
                    return redirect()->route("vendor.kyc.index")->with(['warning' => [$smg]]);
                }
            }
        }
        return $next($request);
    }
}
