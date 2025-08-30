<?php

namespace App\Http\Middleware\Api\V1\Vendor;

use App\Http\Helpers\Response;
use Closure;
use Illuminate\Http\Request;

class AuthGuard
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
        if(auth()->guard("vendor_api")->check()) return Response::error(['You are already authenticated vendor']);
        return $next($request);
    }
}
