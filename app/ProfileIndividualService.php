<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfileIndividualService extends Model
{
    protected $fillable = ['name', 'price'];
    public $timestamps = false;
}
