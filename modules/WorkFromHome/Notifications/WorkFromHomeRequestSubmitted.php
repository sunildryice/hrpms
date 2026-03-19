<?php

namespace Modules\WorkFromHome\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\WorkFromHome\Enums\WorkFromHomeTypes;
use Modules\WorkFromHome\Models\WorkFromHome;


class WorkFromHomeRequestSubmitted extends Notification
{
    use Queueable;

    private $workFromHomeRequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        WorkFromHome $workFromHomeRequest
    ) {
        $this->workFromHomeRequest = $workFromHomeRequest;
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
        $url = route('approve.wfh.requests.show', $this->workFromHomeRequest->id);
        $typeLabel = WorkFromHomeTypes::options()[$this->workFromHomeRequest->type] ?? ucfirst(str_replace('_', ' ', $this->workFromHomeRequest->type));
        return (new MailMessage)
            ->subject("New {$typeLabel} request: " . $this->workFromHomeRequest->getRequestId())
            ->greeting('Dear ' . $this->workFromHomeRequest->approver?->employee?->getFullName() . ',')
            ->line("You have a new {$typeLabel} request awaiting your approval. Please find the details below:")
            ->line('Request ID: ' . $this->workFromHomeRequest->getRequestId())
            ->line('Type: ' . $typeLabel)
            ->line('Requester: ' . $this->workFromHomeRequest->getRequesterName())
            ->line('Start Date: ' . $this->workFromHomeRequest->getStartDate())
            ->line('End Date: ' . $this->workFromHomeRequest->getEndDate())
            ->line('Total Days: ' . $this->workFromHomeRequest->getWorkFromHomeDuration())
            ->line('Reason: ' . ($this->workFromHomeRequest->reason ?: 'N/A'))
            ->action('View work from home request', $url)
        ;
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
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        event(new NotificationPushed());
        $typeLabel = \Modules\WorkFromHome\Enums\WorkFromHomeTypes::options()[$this->workFromHomeRequest->type] ?? ucfirst(str_replace('_', ' ', $this->workFromHomeRequest->type));
        return [
            'work_from_home_id' => $this->workFromHomeRequest->id,
            'link' => route('approve.wfh.requests.show', $this->workFromHomeRequest->id),
            'alternate_link' => route('wfh.requests.show', $this->workFromHomeRequest->id),
            'subject' => "Work from home request {$typeLabel} {$this->workFromHomeRequest->id} has been submitted. Requester : {$this->workFromHomeRequest->requester->full_name}",
        ];
    }
}
