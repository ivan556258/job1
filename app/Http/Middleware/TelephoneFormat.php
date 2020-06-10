<?php

namespace App\Http\Middleware;

use Closure;

class TelephoneFormat
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
        if($request->has('telephone')) {
            $request->merge(['telephone' => telephoneFormat($request->input('telephone'))]);
        }

        return $next($request);
    }
}
