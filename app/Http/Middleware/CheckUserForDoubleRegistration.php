<?php

namespace App\Http\Middleware;

use App\Exceptions\DoubleRegistration;
use App\UserDeviceInformation;
use Closure;
use Illuminate\Support\Facades\Cookie;

class CheckUserForDoubleRegistration
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws DoubleRegistration
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
        $subnet = explode('.', $request->ip());
        $canvas = md5($request->input('fpcanvas'));
        $webgl = md5($request->input('fpwebgl'));
        $extra = md5($request->input('fpextra') . $subnet[0] . $subnet[1]);
        $userDevices = UserDeviceInformation::where(function ($query) use ($canvas, $webgl, $extra) {
            $query->where('fpcanvas', $canvas)->orWhere('fpwebgl', $webgl)
                ->orWhere('fpextra', $extra);
        })->whereHas('user', function ($query) {
            $query->whereNotNull('email_verified_at');
        })->first();

        if ($userDevices) {
            throw new DoubleRegistration([
                'canvasHash' => $canvas,
                'webGLHash' => $webgl,
                'extra' => $extra,
                'ip' => $request->ip(),
                'user' => $userDevices->user
            ]);
        }


        return $next($request);
    }
}
