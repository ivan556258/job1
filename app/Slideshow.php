<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Slideshow extends Model
{
    protected $fillable = ['name', 'country_id', 'city_id'];

    public $timestamps = false;

    public function images()
    {
        return $this->hasMany(SlideshowImage::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
