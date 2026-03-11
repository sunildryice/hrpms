<?php

namespace Modules\LeaveRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\LeaveRequest\Models\LeaveRequest;

class LeaveRequestSubstitute extends Notification
{
	use Queueable;

	private $leaveRequest;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct(
		LeaveRequest $leaveRequest
	) {
		$this->leaveRequest = $leaveRequest->load([
			'leaveType',
			'requester',
		]);
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed  $notifiable
	 * @return array
	 */
	public function via($notifiable)
	{
		return ['mail', 'database'];
	}

	/**
	 * Get the mail representation of the notification.
	 *
	 * @param  mixed  $notifiable
	 * @return \Illuminate\Notifications\Messages\MailMessage
	 */
	public function toMail($notifiable)
	{
		$url = route('leave.requests.detail', $this->leaveRequest->id);

		return (new MailMessage)
			->greeting('Hey ' . ($notifiable->getFullName() ?? $notifiable->full_name) . ',')
			->line('You have been assigned as a substitute for a leave request (' . $this->leaveRequest->getLeaveType() . ').')
			->line('Employee : ' . $this->leaveRequest->getRequesterName())
			->line('Leave dates : ' . $this->leaveRequest->start_date->format('d M Y') . ' to ' . $this->leaveRequest->end_date->format('d M Y'))
			->line('Reason : ' . $this->leaveRequest->remarks)
            ->line('Click the button below to view the detail leave request ')
			->action('View leave request', $url);
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

	/**
	 * Get the database representation of the notification.
	 *
	 * @param  mixed  $notifiable
	 * @return array
	 */
	public function toDatabase($notifiable)
	{
         event(new NotificationPushed());
		return [
			'leave_request_id' => $this->leaveRequest->id,
			'link' => route('leave.requests.detail', $this->leaveRequest->id),
			'alternate_link' => route('leave.requests.detail', $this->leaveRequest->id),
			'subject' => 'You are assigned as substitute for leave request ' . $this->leaveRequest->getLeaveNumber() . '. Requester : ' . $this->leaveRequest->getRequesterName(),
		];
	}





}