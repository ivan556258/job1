<?php

namespace App;

use App\Scopes\ProfileImagesSortScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ProfileImage extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new ProfileImagesSortScope);
    }

    protected $fillable = ['image', 'active', 'main', 'advertising_platform', 'order', 'alt', 'moderate_message'];

    public function scopeNotConfirmedImages($query)
    {
        $query->where('active', 0);
    }

    public function setActiveAttribute($val)
    {
        if(Auth::user()->level() > 3) {
            $this->attributes['active'] = $val;
        }
    }
}
