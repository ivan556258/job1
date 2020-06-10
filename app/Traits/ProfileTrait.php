<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

trait ProfileTrait
{
    protected function getUserId()
    {
        return $this->user_id;
    }

    protected function getImages()
    {
        return $this->images;
    }

    public function ownerProfile()
    {
        return $this->getUserId() == Auth::id();
    }

    public function hasUnActiveImages()
    {
        return $this->getImages()->where('active', 0)->count();
    }

    //Проверка на то оплачено ли размещение без подтв фото или подтв фото одобрены админом
    public function isVerified()
    {
        return $this->verification || ($this->verification_paid && $this->verification_at->addDays(30) >= Carbon::now());
    }
}