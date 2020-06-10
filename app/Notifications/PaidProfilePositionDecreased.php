<?php

namespace App\Notifications;

use App\Profile;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PaidProfilePositionDecreased extends Notification
{
    use Queueable;
    private $profile;
    private $position;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Profile $profile, int $position)
    {
        $this->profile = $profile;
        $this->position = $position + 1;
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
        return (new MailMessage)->subject('Позиция анкеты понижена в выдаче')
            ->view('notifications.email.paid-profile-advertising-decreased', ['profile' => $this->profile, 'position' => $this->position]);
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
