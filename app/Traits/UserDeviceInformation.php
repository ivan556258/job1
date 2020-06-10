<?php


namespace App\Traits;


use App\User;
use Illuminate\Support\Facades\Request;

trait UserDeviceInformation
{
    public function setUserDeviceInformation(array $data, User $user)
    {
        if(!empty($data['fpextra'])) {
            $subnet = explode('.', Request::ip());
            $data['fpextra'] .= $subnet[0] . $subnet[1];
        }

        $user->devicesInformation()->create($data);
    }
}