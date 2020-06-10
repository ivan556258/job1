<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SalonImage extends Model
{
    protected $fillable = ['image', 'active', 'main', 'logo', 'order', 'moderate_message'];
    public $timestamps = false;

    public function setActiveAttribute($val)
    {
        if(Auth::user()->level() > 3) {
            $this->attributes['active'] = $val;
        }
    }

    public function scopeNotConfirmedImages($query)
    {
        $query->where('active', 0);
    }
}
