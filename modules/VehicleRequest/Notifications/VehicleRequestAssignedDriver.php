<?php

namespace Modules\VehicleRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\VehicleRequest\Models\VehicleRequest;

class VehicleRequestAssignedDriver extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        protected VehicleRequest $vehicleRequest
    ) {
        $this->vehicleRequest = $vehicleRequest;
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
        $url = route('vehicle.requests.show', $this->vehicleRequest->id);

        return (new MailMessage)
            ->greeting('Dear ' . $notifiable->getFullName() . ',')
            ->line('You have been assigned as a driver for a vehicle request.')
            ->line('Request Number : ' . $this->vehicleRequest->getVehicleRequestNumber())
            ->line('Requester : ' . $this->vehicleRequest->getRequesterName())
            ->line('Vehicle : ' . $this->vehicleRequest->assignedVehicle?->getVehicleNumberWithCapacity())
            ->line('Travel dates : ' . ($this->vehicleRequest->start_datetime?->format('d M Y h:i A') ?? '-') . ' to ' . ($this->vehicleRequest->end_datetime?->format('d M Y h:i A') ?? '-'))
            ->line('Purpose : ' . ($this->vehicleRequest->purpose_of_travel ?? '-'))
            ->action('View Assignment', $url);
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
            'vehicle_request_id' => $this->vehicleRequest->id,
            'link' => route('vehicle.requests.show', $this->vehicleRequest->id),
            'subject' => 'You have been assigned as driver for vehicle request ' . $this->vehicleRequest->getVehicleRequestNumber() . '. Requester : ' . $this->vehicleRequest->getRequesterName(),
        ];
    }
}
