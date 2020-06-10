<?php

namespace App\Notifications;

use App\Profile;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProfileNotVisibleNow extends Notification
{
    use Queueable;

    private $profile;
    private $reason;

    /**
     * Create a new notification instance.
     *
     * @param Profile $profile
     * @param string $reason
     */
    public function __construct(Profile $profile, string $reason)
    {
        $this->profile = $profile;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Ваш профиль больше не показывается')
            ->view('notifications.email.profile-not-visible-now',
                ['profile' => $this->profile, 'reason' => $this->reason]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
