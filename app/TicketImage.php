<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketImage extends Model
{
    protected $fillable = ['image', 'ticket_message_id'];
}
