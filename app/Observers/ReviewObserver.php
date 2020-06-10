<?php


namespace App\Observers;


use App\Events\ProfileReviewWasUpdated;
use App\Review;

class ReviewObserver
{
    public function updated(Review $review)
    {
        event(new ProfileReviewWasUpdated($review));
    }
}