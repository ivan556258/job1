<?php

namespace App;

use App\Scopes\MetroScope;
use Illuminate\Database\Eloquent\Model;

class Metro extends Model
{
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new MetroScope());
    }

    protected $fillable = ['name', 'city_id'];

    public $timestamps = false;

    public function city() {
        return $this->belongsTo(City::class);
    }

    public function scopeSearchMetro($query, string $metro_name, int $city_id) {
        return $query->where('name', 'LIKE', '%'.$metro_name.'%')->where('city_id', $city_id)->limit(5);
    }
}
