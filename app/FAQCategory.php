<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FAQCategory extends Model
{
    protected $fillable = ['name'];
    public $timestamps = false;

    public function questions()
    {
        return $this->hasMany(FAQ::class);
    }
}
