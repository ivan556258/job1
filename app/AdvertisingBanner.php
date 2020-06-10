<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AdvertisingBanner extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'date_start',
        'date_finish',
        'city_id',
        'country_id',
        'user_id',
        'link'
    ];

    protected $casts = [
        'date_start' => 'date',
        'date_finish' => 'date'
    ];

    public function bannerable()
    {
        return $this->morphTo();
    }

    public function scopeToday($query, int $city_id, int $country_id)
    {
        $query->where('date_start', '<=', Carbon::today())
            ->where('date_finish', '>', Carbon::today())->where(function ($query) use($city_id, $country_id) {
            $query->where('city_id', $city_id)
                ->orWhere('country_id', $country_id);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
