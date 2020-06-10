<?php

namespace App;

use App\Scopes\TicketScope;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new TicketScope);
    }

    protected $fillable = ['title', 'subject', 'status_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function lastMessage()
    {
        return $this->hasOne(TicketMessage::class)->latest();
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
