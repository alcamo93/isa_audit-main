<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class ReminderContracts extends Notification implements ShouldQueue
{
	use Queueable, SerializesModels;

	public $notifyData;
	public $isAddmin = false;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($notifyData, $isAddmin = false)
	{
		$this->notifyData = $notifyData;
		$this->isAddmin = $isAddmin;
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
		$view = $this->isAddmin ? 'mails.reminders.expiredContractAdmin' : 'mails.reminders.expiredContract';
		return (new MailMessage)->markdown($view, ['data' => $this->notifyData])
			->subject('Recordatorio de vencimiento de contrato');
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
