<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDeviceInformation extends Model
{
    protected $fillable = ['fpcanvas', 'fpwebgl', 'fpextra'];

    public function setFpcanvasAttribute($val)
    {
        $this->attributes['fpcanvas'] = md5($val);
    }

    public function setFpwebglAttribute($val)
    {
        $this->attributes['fpwebgl'] = md5($val);
    }

    public function setFpextraAttribute($val)
    {
        $this->attributes['fpextra'] = md5($val);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
