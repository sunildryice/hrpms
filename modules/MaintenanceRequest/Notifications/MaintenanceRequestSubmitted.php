<?php

namespace Modules\MaintenanceRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\MaintenanceRequest\Models\MaintenanceRequest;

class MaintenanceRequestSubmitted extends Notification
{
    use Queueable;

    private $maintenanceRequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        MaintenanceRequest $maintenanceRequest
    ) {
        $this->maintenanceRequest = $maintenanceRequest;
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
        $url = route('review.maintenance.requests.create', $this->maintenanceRequest->id);

        return (new MailMessage)
            ->greeting('Dear ' . $this->maintenanceRequest->getApproverName() . ',')
            ->line('You have a new maintenance request awaiting your review.')
            ->line('Request Number: ' . $this->maintenanceRequest->getMaintenanceRequestNumber())
            ->line('Requester: ' . $this->maintenanceRequest->getRequesterName())
            ->line('Request date: ' . $this->maintenanceRequest->request_date)
            ->line('Reason: ' . $this->maintenanceRequest->remarks)
            ->action('Review Maintenance Request', $url);
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
        return [
            'maintenance_id' => $this->maintenanceRequest->id,
            'link' => route('review.maintenance.requests.create', $this->maintenanceRequest->id),
            'alternate_link' => route('maintenance.requests.view', encrypt($this->maintenanceRequest->id)),
            'subject' => 'Maintenance request ' . $this->maintenanceRequest->getMaintenanceRequestNumber() . ' requested by ' . $this->maintenanceRequest->getRequesterName() . ' has been submitted for your approval.'
        ];
    }
}
