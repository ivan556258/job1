<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $fillable = ['channel', 'notification', 'active'];
    public $timestamps = false;

    public function scopeNotificationStatus($query, string $notification, string $channel = 'email')
    {
        $query->where('channel', $channel)->where('notification', $notification);
    }
}
