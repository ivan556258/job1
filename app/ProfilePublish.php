<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfilePublish extends Model
{
    public $timestamps = false;
    protected $fillable = ['socials', 'advertising_platform', 'special_color', 'telephone_search'];
}
