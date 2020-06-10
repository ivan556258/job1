<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    protected $fillable = [
        'time_from',
        'time_to',
        'day'
    ];

    public $timestamps = false;

    public function profile() {
        return $this->belongsTo(Profile::class);
    }
}
