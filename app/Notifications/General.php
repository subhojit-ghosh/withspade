<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class General extends Notification
{
    use Queueable;

    public $title;
    public $link;
    public $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($title, $link, $message)
    {
        $this->title = $title;
        $this->link = $link;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
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
            'title' => $this->title,
            'link' => $this->link,
            'message' => $this->message,
        ];
    }
}
