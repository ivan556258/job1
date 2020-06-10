<?php


namespace App\Traits;


trait TelephoneTrait
{
    public function hasTelephone(string $telephone_code, string $telephone)
    {
        return $this->telephones->where('telephone', $telephone)->where('telephone_code', $telephone_code)->isNotEmpty();
    }
}
