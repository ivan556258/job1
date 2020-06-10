<?php

namespace App\Http\Middleware;

use App\Exceptions\AccountForbidden;
use Closure;
use Illuminate\Support\Facades\Auth;

class AccountVerified
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws AccountForbidden
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if(empty(telephoneInstance($user)->telephone) || !$user->city_id || $user->getRoles()->isEmpty()) {
            throw new AccountForbidden('Для совершения этого действия необходимо <a href="'.route('account.index').'">заполнить информацию об аккаунте</a>');
        }

        return $next($request);
    }
}
