<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfileAdvertising extends Model
{
    protected $fillable = [
        'cost'
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function scopeTopProfiles($query, array $profileIds)
    {
        $query->whereIn('profile_id', $profileIds)->orderByDesc('cost')->limit(config('general.advertising.indexTopCount'));
    }
}
