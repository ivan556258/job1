<?php

namespace App;

use App\Scopes\SalonOrderScope;
use Grechanyuk\Favorite\Traits\FavoriteableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class Salon extends Model
{
    use FavoriteableTrait;

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SalonOrderScope());
    }

    protected $fillable = [
        'name',
        'description',
        'image',
        'address',
        'advantages',
        'map_coordinates',
        'elevated_at',
        'active',
        'user_id'
    ];

    protected $casts = [
        'advantages' => 'array',
        'elevated_at' => 'datetime'
    ];

    public function image()
    {
        return $this->hasOne(SalonImage::class)->where('main', 1);
    }

    public function images()
    {
        return $this->hasMany(SalonImage::class);
    }

    public function extraImages()
    {
        return $this->images()->where('logo', 0)
            ->where('main', 0);
    }

    public function logo()
    {
        return $this->hasOne(SalonImage::class)->where('logo', 1);
    }

    public function videos()
    {
        return $this->hasMany(SalonVideo::class);
    }

    public function metros()
    {
        return $this->belongsToMany(Metro::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeNewest($query)
    {
        $query->where('created_at', '>=', Carbon::now()->addDays(config('general.time.addDays')));
    }

    public function advertisingBanners()
    {
        return $this->morphMany(AdvertisingBanner::class, 'bannerable');
    }
}
