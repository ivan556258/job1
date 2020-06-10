<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfileClient extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'mans',
        'women',
        'children',
        'pairs',
        'mans_age_from',
        'mans_age_to',
        'women_age_from',
        'women_age_to',
        'children_age_from',
        'children_age_to'
    ];
}
