<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    public $timestamps = false;
    protected $fillable = ['skey', 'svalue', 'stype', 'stype_id', 'city_id', 'country_id'];
}
