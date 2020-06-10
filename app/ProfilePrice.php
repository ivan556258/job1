<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfilePrice extends Model
{
    protected $fillable = ['type', 'work_time_id', 'price'];
    public $timestamps = false;

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function scopePricesIn($query)
    {
        $query->where('type', 'in');
    }

    public function scopePricesOut($query)
    {
        $query->where('type', 'out');
    }

    public function work_time()
    {
        return $this->belongsTo(WorkTime::class);
    }
}
