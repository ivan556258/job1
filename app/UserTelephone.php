<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserTelephone extends Model
{
    protected $fillable = [
        'telephone',
        'telephone_code',
        'telephone_confirmed',
        'main'
    ];

    public function scopeSearch($query, string $telephoneCode, string $telephone)
    {
        $query->where('telephone_code', $telephoneCode)->where('telephone', $telephone);
    }
}
