<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Convenience extends Model
{
    public $timestamps = false;
    protected $fillable = ['name'];
}
