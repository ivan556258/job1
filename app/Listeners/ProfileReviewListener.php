<?php


namespace App\Listeners;


use App\Events\ProfileReviewWasUpdated;
use App\Notifications\NewProfileReview;

class ProfileReviewListener extends Listener
{
    public function onProfileReviewWasUpdated(ProfileReviewWasUpdated $event)
    {
        $review = $event->getReview();
        $review->load(['profile.user']);

        $notification = $review->profile->user->notificationSettings()->notificationStatus('newReview', 'email')->first();

        if(!$review->notify && $review->active && $notification && $notification->active) {
            $review->profile->user->notify(new NewProfileReview($review));
            $review->notify = 1;
            $review->save();
        }
    }
}