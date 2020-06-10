<?php

namespace App\Http\Middleware;

use App\Exceptions\NotEnoughBalance;
use Closure;
use Illuminate\Support\Facades\Auth;

class ActivateSalon
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws NotEnoughBalance
     */
    public function handle($request, Closure $next)
    {
        $salon = Auth::user()->salon;

        if($salon && !$salon->active) {
            if(Auth::user()->actualBalance() < setting('salon.activate', 'prices', 0)) {
                throw new NotEnoughBalance(setting('salon.activate', 'prices', 0));
            }
        }

        return $next($request);
    }
}
