<?php

namespace App\Http\Middleware;

use App\Exceptions\NotEnoughBalance;
use App\Exceptions\TelephoneUnconfirmed;
use App\Facades\ProfileService;
use App\Facades\ProfileTransactions;
use App\Scopes\ProfilePaidScope;
use Closure;
use Illuminate\Support\Facades\Auth;

class CreateProfileChecker
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
		
        $profile = $request->route()->parameter('accountProfile');
		
		if($profile->active===1){
			return $next($request);
		}

        if(empty(telephoneInstance($profile->user)->telephone_confirmed)) {
            throw new TelephoneUnconfirmed();
        }

        $profiles = Auth::user()->profiles();
        $totalCost = 0;

        if (!$profile->paid) {
            $cost = ProfileService::getCostOfProfiles($profiles->count());
            $totalCost += $cost;
        }

        if(!$profile->isVerified()) {
            $verification_cost = setting(Auth::user()->getRole()->slug.'.verification_cost', 'prices', 0);
            $totalCost += $verification_cost;
        }

        if (Auth::user()->actualBalance() < $totalCost) {
            $message = '';
            if(!$profile->isVerified()) {
                if($profile->verification_images->isNotEmpty()) {
                    $message = 'Или дождитесь проверки фото';
                } else { 
                    $message = 'Или добавьте проверочные фото';
                }
            }
            throw new NotEnoughBalance($totalCost, null, $message);
        }

        if(!$profile->paid && isset($cost)) {
            ProfileTransactions::setPaid($profile, $cost);
        }

        if(!$profile->isVerified() && isset($verification_cost)) {
            ProfileTransactions::setVerification($profile, $verification_cost);
        }

        $request->route()->setParameter('profile', $profile);

        return $next($request);
    }
}
