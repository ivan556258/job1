<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\Subdomain::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'role' => \jeremykenedy\LaravelRoles\Middleware\VerifyRole::class,
        'permission' => \jeremykenedy\LaravelRoles\Middleware\VerifyPermission::class,
        'level' => \jeremykenedy\LaravelRoles\Middleware\VerifyLevel::class,
        'adminPanel' => \App\Http\Middleware\AdminPanel::class,
        'accountVerified' => \App\Http\Middleware\AccountVerified::class,
        'telephoneFormat' => \App\Http\Middleware\TelephoneFormat::class,
        'profileScopes' => \App\Http\Middleware\ProfileScopes::class,
        'profileStatistic' => \App\Http\Middleware\ProfileStatistic::class,
        'createProfileChecker' => \App\Http\Middleware\CreateProfileChecker::class,
        'profileMaxCount' => \App\Http\Middleware\ProfileMaxCount::class,
        'salonScopes' => \App\Http\Middleware\SalonsScopes::class,
        'salonActivate' => \App\Http\Middleware\ActivateSalon::class,
        'freekassa' => \Grechanyuk\FreeKassa\Middlewares\FreeKassaNotificationChecker::class,
        'ownerProfileForComments' => \App\Http\Middleware\OwnerProfileForComments::class,
        'salonActivated' => \App\Http\Middleware\SalonActivated::class,
        'checkUserForDoubleRegistration' => \App\Http\Middleware\CheckUserForDoubleRegistration::class,

    ];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\Authenticate::class,
        \Illuminate\Session\Middleware\AuthenticateSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
    ];
}
