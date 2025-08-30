<?php

namespace App\Http\Middleware;

use App\Http\Helpers\Api\helpers as Helpers;
use App\Providers\Admin\BasicSettingsProvider;
use Closure;
use Illuminate\Http\Request;

class RegistrationPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $slug)
    {
        $basic_settings = BasicSettingsProvider::get();
        if ($request->expectsJson()) {
            if ($basic_settings->user_registration != true) {
                $message = ['error' => [__("Registration Option Currently Off")]];
                return Helpers::error($message);
            }
            return $next($request);
        }

        if ($slug === 'vendor') {
            if($basic_settings->vendor_registration != true) return back()->withInput()->with(['warning' => [__("Registration Option Currently Off")]]);
        }
        else{
            if($basic_settings->user_registration != true) return back()->withInput()->with(['warning' => [__("Registration Option Currently Off")]]);
        }
        return $next($request);
    }
}
