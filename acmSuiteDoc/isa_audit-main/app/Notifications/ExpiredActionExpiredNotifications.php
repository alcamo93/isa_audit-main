<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ExpiredActionExpiredNotifications extends Notification
{
    use Queueable;

    public $notifyData = null;
    public $view = null;
    public $subject = null;
    public $notifyInfo = null;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($notifyData, $view, $subject)
    {
        $this->notifyData = $notifyData['mail'];
        $this->view = $view;
        $this->subject = $subject;
        $this->notifyInfo = $notifyData['info'];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)->markdown(
            $this->view, [
                'data' => $this->notifyData
            ]
        )->subject($this->subject);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return $this->notifyInfo;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([]);
    }
}
