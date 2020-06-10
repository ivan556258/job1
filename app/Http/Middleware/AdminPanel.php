<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;

class AdminPanel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Cookie::get('sideBar') === 'min') {
            View::composer('layouts.admin', function($view) {
                $view->with('sideBarClass', 'col-xl-1 col-md-3');
                $view->with('sideBarChildClass', 'style="display: none;"');
                $view->with('sideBarHideBtn', 'rotate');
                $view->with('mainContentBlock', 'col-xl-11 col-md-9');
            });
        }

        return $next($request);
    }
}
