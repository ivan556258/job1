<?php

namespace App\Providers;

use App\Events\DeletedImageListener;
use App\Events\ImageDeleted;
use App\Events\ImageUploaded;
use App\Events\MonthlyCostWasChanged;
use App\Events\ProfileAdvertisingWasUpdated;
use App\Events\ProfileImagesWasModerated;
use App\Events\ProfileNotVisibleNow;
use App\Events\ProfileReviewWasUpdated;
use App\Events\ProfileWasBlocked;
use App\Listeners\MonthlyCostChangedListener;
use App\Listeners\NotificationSentListener;
use App\Listeners\ProfileAdvertisingListener;
use App\Listeners\ProfileImagesListener;
use App\Listeners\ProfileListener;
use App\Listeners\ProfileReviewListener;
use App\Listeners\SuccessNotificationWasTakedListeners;
use App\Listeners\UploadImageListener;
use Grechanyuk\FreeKassa\Events\SuccessNotificationWasTaked;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Notifications\Events\NotificationSent;
use JhaoDa\SocialiteProviders\MailRu\MailRuExtendSocialite;
use JhaoDa\SocialiteProviders\Odnoklassniki\OdnoklassnikiExtendSocialite;
use SocialiteProviders\Facebook\FacebookExtendSocialite;
use SocialiteProviders\Google\GoogleExtendSocialite;
use SocialiteProviders\Instagram\InstagramExtendSocialite;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\VKontakte\VKontakteExtendSocialite;
use SocialiteProviders\Yandex\YandexExtendSocialite;
use UniSharp\LaravelFilemanager\Events\ImageWasUploaded;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ImageWasUploaded::class => [
            UploadImageListener::class
        ],
        ImageUploaded::class => [
            UploadImageListener::class
        ],
        ImageDeleted::class => [
           DeletedImageListener::class
        ],
        SocialiteWasCalled::class => [
            VKontakteExtendSocialite::class,
            InstagramExtendSocialite::class,
            GoogleExtendSocialite::class,
            FacebookExtendSocialite::class,
            YandexExtendSocialite::class,
            OdnoklassnikiExtendSocialite::class,
            MailRuExtendSocialite::class,
        ],
        MonthlyCostWasChanged::class => [
            MonthlyCostChangedListener::class,
        ],
        SuccessNotificationWasTaked::class => [
            SuccessNotificationWasTakedListeners::class,
        ],
        ProfileReviewWasUpdated::class => [
            ProfileReviewListener::class,
        ],
        ProfileImagesWasModerated::class => [
            ProfileImagesListener::class,
        ],
        ProfileAdvertisingWasUpdated::class => [
            ProfileAdvertisingListener::class,
        ],
        ProfileWasBlocked::class => [
            ProfileListener::class
        ],
        ProfileNotVisibleNow::class => [
            ProfileListener::class
        ],
        NotificationSent::class => [
            NotificationSentListener::class
        ]

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
