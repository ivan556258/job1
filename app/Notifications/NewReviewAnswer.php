<?php

namespace App\Notifications;

use App\ReviewAnswer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewReviewAnswer extends Notification
{
    use Queueable;
    private $reviewAnswer;

    /**
     * Create a new notification instance.
     *
     * @param ReviewAnswer $reviewAnswer
     */
    public function __construct(ReviewAnswer $reviewAnswer)
    {
        $this->reviewAnswer = $reviewAnswer;
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
                    ->subject('Новый ответ на отзыв')
            ->view('notifications.email.new_review_answer', ['reviewAnswer' => $this->reviewAnswer]);
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
