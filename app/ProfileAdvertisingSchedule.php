<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfileAdvertisingSchedule extends Model
{
    public $timestamps = false;

    protected $fillable = ['day', 'time_from', 'time_to', 'city_id'];

    public function scopeProfilesByTime($query, $time_from, $time_to, $day)
    {
        $query->where('time_from', '<', $time_from)
            ->where('time_to', '>', $time_to)
            ->where('day', $day);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
