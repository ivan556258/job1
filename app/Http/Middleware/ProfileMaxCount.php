<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ProfileMaxCount
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $profilesCount = Auth::user()->profiles()->withoutGlobalScopes()->count();
        $userRoleSlug = Auth::user()->getRole()->slug;

        if (setting($userRoleSlug . '.max_profiles', 'counts') && setting($userRoleSlug . '.max_profiles', 'counts') <= $profilesCount) {
            abort(403);
        }

        return $next($request);
    }
}
