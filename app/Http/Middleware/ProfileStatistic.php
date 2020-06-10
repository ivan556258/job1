<?php

namespace App\Http\Middleware;

use App\Facades\ProfileStatistic as ProfileStatisticFacade;
use App\Profile;
use Closure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\TerminableInterface;

class ProfileStatistic implements TerminableInterface
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
        return $next($request);
    }

    /**
     * Terminates a request/response cycle.
     *
     * Should be called after sending the response and before shutting down the kernel.
     * @param Request $request
     * @param Response $response
     */
    public function terminate(Request $request, Response $response)
    {
        if(!empty($request->route()->parameter('frontProfile'))) {
            if($request->route()->parameter('frontProfile') instanceof Profile) {
                ProfileStatisticFacade::insert($request, $request->route()->parameter('frontProfile'));
            }
        }
    }
}
