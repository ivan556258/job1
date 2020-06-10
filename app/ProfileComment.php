<?php

namespace App;

use App\Scopes\ProfileCommentScope;
use App\Traits\AssessmentableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ProfileComment extends Model
{
    use AssessmentableTrait;

    protected $fillable = ['profile_id', 'parent_id', 'comment', 'user_id', 'hidden'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new ProfileCommentScope());
    }

    public function answers()
    {
        return $this->hasMany(ProfileComment::class, 'parent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function scopeRoot($query)
    {
        $query->whereNull('parent_id');
    }
}
