<?php

namespace App;

use App\Facades\Domain;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['name', 'prefix', 'region_id', 'general'];

    public $timestamps = false;

    public function setNameAttribute($val)
    {
        $this->attributes['name'] = ucfirst($val);
    }

    public function scopeSearchCity($query, string $city)
    {
        return $query->where('name', 'LIKE', '%' . $city . '%')->limit(5);
    }

    public function metros()
    {
        return $this->hasMany(Metro::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function profiles()
    {
        return $this->hasManyThrough(Profile::class, User::class);
    }

    public function salons()
    {
        return $this->hasManyThrough(Salon::class, User::class);
    }
}
