<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ProfileStatistic extends Model
{
    protected $fillable = ['fingerprint', 'profile_id', 'created_at'];

    public function scopeStatisticByTime($query, $date_from, $date_to)
    {
        $query->where('created_at', '>=', $date_from)
            ->where('created_at', '<=', $date_to);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
