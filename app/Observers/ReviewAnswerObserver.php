<?php
namespace App\Observers;

use App\Notifications\NewReviewAnswer;
use App\ReviewAnswer;

class ReviewAnswerObserver
{
    public function created(ReviewAnswer $reviewAnswer)
    {
        $this->notificate($reviewAnswer);
    }

    public function updated(ReviewAnswer $reviewAnswer)
    {
        $this->notificate($reviewAnswer);
    }

    private function notificate(ReviewAnswer $reviewAnswer)
    {
        if($reviewAnswer->active && !$reviewAnswer->notified) {
            $reviewAnswer->load(['user', 'review.user', 'review.user.notificationSettings']);
            $user = $reviewAnswer->review->user;
            $notification = $user->notificationSettings()->notificationStatus('reviewAnswer', 'email')->first();
            $reviewAnswer->update(['notified' => 1]);

            if($notification && $notification->active) {
                $user->notify(new NewReviewAnswer($reviewAnswer));
            }
        }
    }
}