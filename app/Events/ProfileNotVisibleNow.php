<?php


namespace App\Events;


use App\Profile;
use Illuminate\Queue\SerializesModels;

class ProfileNotVisibleNow
{
    use SerializesModels;

    private $profile;
    private $reason;

    public function __construct(Profile $profile, string $reason)
    {
        $this->profile = $profile;
        $this->reason = $reason;
    }

    /**
     * @return mixed
     */
    public function getProfile(): Profile
    {
        return $this->profile;
    }

    /**
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }
}