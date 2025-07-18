<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class RenewalContractNotification extends Notification implements ShouldQueue
{
	use Queueable, SerializesModels;

	protected $notifyData;
	
	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($notifyData)
	{
		$this->notifyData = $notifyData;
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
		return (new MailMessage)->markdown(
			'mails.reminders.renewalContract', ['data' => $this->notifyData]
		)->subject('Notificación de renovación de contrato');
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