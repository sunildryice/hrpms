<?php

namespace Modules\MaintenanceRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\MaintenanceRequest\Models\MaintenanceRequest;

class MaintenanceRequestReturned extends Notification
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
    )
    {
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
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = route('maintenance.requests.index', $this->maintenanceRequest->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Maintenance request '.$this->maintenanceRequest->getMaintenanceRequestNumber().' has been returned.')
            ->action('View Maintenance Request ', $url)
            ->line('Thank you for using our application!');
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
            'link'=>route('maintenance.requests.view', encrypt($this->maintenanceRequest->id)),
            'subject'=> 'Maintenance request '.$this->maintenanceRequest->getMaintenanceRequestNumber().' has been returned.'
        ];
    }

}
