<?php

namespace App\Observers;

use App\Events\ProfileWasBlocked;
use App\Notifications\Admin\NewProfile;
use App\Profile;
use App\UpdateSiteMap;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

class ProfileObserver
{
    public function updating(Profile $profile)
    {
        if ($profile->isDirty('active') && $profile->active) {
            $profile->activated_at = Carbon::now();
        }
    }

    public function deleting(Profile $profile)
    {
        $profile->favorites()->delete();
    }

    public function created(Profile $profile)
    {
        if($profile->active) {
            $this->updateSiteMaps($profile);
        }

        Notification::route('mail', config('app.adminNotificationEmail'))
            ->notify(new NewProfile($profile));
    }

    public function updated(Profile $profile)
    {
        if($profile->isDirty('active') && $profile->active) {
            $this->updateSiteMaps($profile);
        }

        if($profile->isDirty('block') && $profile->block) {
            event(new ProfileWasBlocked($profile));
        }
    }

    public function deleted(Profile $profile)
    {
        $this->updateSiteMaps($profile);
    }

    private function updateSiteMaps(Profile $profile)
    {
        $city_id = $profile->user->city_id;
        $siteMapQueue = UpdateSiteMap::where('city_id', $city_id)
            ->orWhereNull('city_id');

        if (!$siteMapQueue->exists()) {
            UpdateSiteMap::create([
                'city_id' => $city_id
            ]);
        }
    }
}
