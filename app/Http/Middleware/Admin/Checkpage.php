<?php

namespace App\Http\Middleware\Admin;

use Closure;
use Illuminate\Http\Request;

class Checkpage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next,$slug)
    {
        $permission = pageEnable($slug);
        if($permission == false){
          abort(404);
        }
        return $next($request);
    }
}
