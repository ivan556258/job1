<?php


namespace App\Observers;


use App\Notifications\Admin\NewTicket;
use App\Ticket;
use Illuminate\Support\Facades\Notification;

class TicketObserver
{
    public function created(Ticket $ticket)
    {
        Notification::route('mail', config('app.adminNotificationEmail'))
            ->notify(new NewTicket($ticket));
    }
}
