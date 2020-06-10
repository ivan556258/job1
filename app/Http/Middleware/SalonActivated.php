<?php

namespace App\Http\Middleware;

use App\Exceptions\AccountForbidden;
use Closure;
use Illuminate\Support\Facades\Auth;

class SalonActivated
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
        $user = Auth::user()->load(['salon']);

        if($user->getRoles()->isEmpty() || ($user->getRole()->slug == 'salon' && !$user->salon) || ($user->getRole()->slug == 'salon' && !$user->salon->active)) {
            throw new AccountForbidden('Для совершения этого действия необходимо <a href="'.route('account.salon.index').'">активировать салон</a>');
        }

        return $next($request);
    }
}
