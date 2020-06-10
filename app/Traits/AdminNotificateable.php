<?php


namespace App\Traits;


trait AdminNotificateable
{
    public function getAdminDescription():? string
    {
        return $this->adminDescription ?? null;
    }
}
