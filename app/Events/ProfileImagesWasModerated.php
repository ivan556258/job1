<?php
namespace App\Events;

use App\Profile;
use Illuminate\Queue\SerializesModels;

class ProfileImagesWasModerated
{
    use SerializesModels;

    private $profile;

    public function __construct(Profile $profile)
    {
        $this->profile = $profile;
    }

    /**
     * @return mixed
     */
    public function getProfile(): Profile
    {
        return $this->profile;
    }
}