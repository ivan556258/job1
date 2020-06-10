<?php

namespace App;

use Grechanyuk\CentralBankCurrency\Models\CentralBankCurrency;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = ['name', 'telephone_code', 'icon', 'currency_id'];

    public $timestamps = false;

    public function regions()
    {
        return $this->hasMany(Region::class);
    }

    public function cities()
    {
        return $this->hasManyThrough(City::class, Region::class);
    }

    public function setNameAttribute($val)
    {
        $this->attributes['name'] = ucfirst($val);
    }

    public function currency()
    {
        return $this->belongsTo(CentralBankCurrency::class);
    }
}
