<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MonthlyCostWasChanged extends Notification
{
    use Queueable;
    private $user;
    private $newCost;

    /**
     * Create a new notification instance.
     *
     * @param User $user
     * @param float $newCost
     */
    public function __construct(User $user, float $newCost)
    {
        $this->user = $user;
        $this->newCost = $newCost;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Изменилась стоимость месячного обслуживания')
            ->view('notifications.email.monthly_cost_was_changed', ['user' => $this->user, 'newCost' => $this->newCost]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
