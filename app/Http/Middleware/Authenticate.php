<?php

namespace App\Http\Middleware;

use App\Http\Helpers\Response;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Session;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            if($request->routeIs('admin.*')) {
                return route('admin.login');
            }else if($request->routeIs("user.*")) {
                Session::put('form_data', $request->all());
                return route('user.login');
            }
            else if($request->routeIs("vendor.*")) {
                return route('vendor.register');
            }
            return route('frontend.index');
        }


        return abort(Response::error(['Access denied! Unauthenticated'],[],401));
    }
}
