<?php

namespace App\Notifications;

use App\Country;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LowBalanceNotification extends Notification
{
    use Queueable;
    private $user;
    private $country;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, Country $country)
    {
        $this->user = $user;
        $this->country = $country;
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
            ->subject('Уведомление о снижении баланса')
            ->view('notifications.email.low-balance', ['user' => $this->user, 'country' => $this->country]);
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
