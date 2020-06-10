<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ProfileVideo extends Model
{
    protected $fillable = ['video', 'active'];

    public $timestamps = false;

    public function setActiveAttribute($val)
    {
        if(Auth::check() && Auth::user()->level() > 4) {
            $this->attributes['active'] = $val;
        }
    }
}
