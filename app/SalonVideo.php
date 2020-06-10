<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SalonVideo extends Model
{
    public $timestamps = false;
    protected $fillable = ['active', 'video'];

    public function setActiveAttribute($val)
    {
        if(Auth::user()->level() > 3) {
            $this->attributes['active'] = $val;
        }
    }
}
