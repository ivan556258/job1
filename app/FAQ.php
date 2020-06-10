<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FAQ extends Model
{
    protected $fillable = ['question', 'answer', 'f_a_q_category_id'];
    public $timestamps = false;

    public function category()
    {
        return $this->belongsTo(FAQCategory::class, 'f_a_q_category_id', 'id');
    }

    public function scopeSearch($query, string $question)
    {
        $query->where('question', 'LIKE', '%' . $question . '%');
    }
}
