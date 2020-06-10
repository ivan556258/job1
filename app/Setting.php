<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['set_key', 'set_value', 'set_type'];

    public $timestamps = false;
}
