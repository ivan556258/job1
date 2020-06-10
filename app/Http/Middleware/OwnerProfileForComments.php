<?php

namespace App\Http\Middleware;

use App\ProfileComment;
use Closure;
use Illuminate\Support\Facades\Auth;

class OwnerProfileForComments
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
        if(Auth::check()) {
            $profileComment = ProfileComment::whereKey($request->route()->parameter('profile_comment'))->firstOrFail();

            if($profileComment->profile->user_id == Auth::id()) {
                return $next($request);
            }
        }

        return abort(403);
    }
}
