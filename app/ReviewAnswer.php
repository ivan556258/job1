<?php

namespace App;

use App\Traits\AssessmentableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ReviewAnswer extends Model
{
    use AssessmentableTrait;

    protected $fillable = ['answer', 'active', 'user_id', 'notified'];

    public function setActiveAttribute($val)
    {
        if(Auth::check() && Auth::user()->level() > 4) {
            $this->attributes['active'] = $val;
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function getCreatedAtAttribute($val)
    {
        return Carbon::createFromTimeString($val)->format('d.m.Y в H:i');
    }
}
