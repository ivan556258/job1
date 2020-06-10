<?php

namespace App;

use App\Scopes\ReviewScope;
use App\Traits\AssessmentableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class Review extends Model
{

    use AssessmentableTrait;

    protected $fillable = [
        'review',
        'rating_general',
        'rating_skill',
        'rating_price',
        'rating_place',
        'rating_service',
        'rating_photo_matching',
        'profile_id',
        'user_id',
        'active'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function setActiveAttribute($val)
    {
        if(Auth::user()->level() > 4) {
            $this->attributes['active'] = $val;
        }
    }

    public function answer()
    {
        return $this->hasOne(ReviewAnswer::class);
    }

    public function hasAnswer()
    {
        return $this->answer()->withoutGlobalScopes()->count();
    }

    public function getCreatedAtAttribute($val)
    {
        return Carbon::createFromTimeString($val)->format('d.m.Y в H:i');
    }
}
