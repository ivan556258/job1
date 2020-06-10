<?php

namespace App\Http\Middleware;

use App\Profile;
use App\ProfileImage;
use App\ProfileVideo;
use App\Review;
use App\ReviewAnswer;
use App\Scopes\ProfileCityScope;
use App\Scopes\ProfileHasImageScope;
use App\Scopes\ProfileImagesScope;
use App\Scopes\ProfilesActiveScope;
use App\Scopes\ProfilesBlockScope;
use App\Scopes\ProfileVideoScope;
use App\Scopes\ReviewAnswerScope;
use App\Scopes\ReviewScope;
use Closure;

class ProfileScopes
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
        //TODO refactor to normal GlobalScopes & add ProfilePaidScope
        Profile::addGlobalScope(new ProfileHasImageScope);
        Profile::addGlobalScope(new ProfilesActiveScope);
        Profile::addGlobalScope(new ProfilesBlockScope);
        Profile::addGlobalScope(new ProfileCityScope);
        ProfileImage::addGlobalScope(new ProfileImagesScope);
        ProfileVideo::addGlobalScope(new ProfileVideoScope);
        Review::addGlobalScope(new ReviewScope);
        ReviewAnswer::addGlobalScope(new ReviewAnswerScope);
        return $next($request);
    }
}
