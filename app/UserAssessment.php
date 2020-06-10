<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAssessment extends Model
{
    public $timestamps = false;

    protected $fillable = ['type', 'user_id'];
}
