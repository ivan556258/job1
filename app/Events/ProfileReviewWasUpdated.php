<?php
namespace App\Events;

use App\Review;
use Illuminate\Queue\SerializesModels;

class ProfileReviewWasUpdated
{
    use SerializesModels;

    private $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    /**
     * @return mixed
     */
    public function getReview(): Review
    {
        return $this->review;
    }
}