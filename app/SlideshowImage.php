<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SlideshowImage extends Model
{
    protected $fillable = ['image', 'text', 'button_text', 'link'];

    public $timestamps = false;
}
