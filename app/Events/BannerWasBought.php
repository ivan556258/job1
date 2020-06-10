<?php


namespace App\Events;


class BannerWasBought
{
    private $userAdvertising;

    public function __construct($userAdvertising)
    {
        $this->userAdvertising = $userAdvertising;
    }

    public function userAdvertising()
    {
        return $this->userAdvertising;
    }
}