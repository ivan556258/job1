<?php

namespace App\Observers;

use App\Notifications\NewTicketAnswer;
use App\TicketMessage;

class TicketMessageObserver
{
    public function created(TicketMessage $message)
    {
        $message->ticket->touch();
        $this->notificate($message);
    }

    private function notificate(TicketMessage $message)
    {
        $message->load(['ticket', 'ticket.user']);

        if ($message->user_id != $message->ticket->user_id) {
            $user = $message->ticket->user;
            $notification = $user->notificationSettings()->notificationStatus('ticketAnswer', 'email')->first();
            if ($notification && $notification->active) {
                $user->notify(new NewTicketAnswer($message));
            }
        }
    }
}