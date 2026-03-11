<?php

namespace Modules\VehicleRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\VehicleRequest\Models\VehicleRequest;

class VehicleRequestSubmitted extends Notification
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
        if ($this->vehicleRequest->vehicle_request_type_id == 1) {
            // Office Vehicle
            $url = route('assign.vehicle.requests.create', $this->vehicleRequest->id);
        } else {
            // Hire Vehicle
            $url = route('approve.vehicle.requests.create', $this->vehicleRequest->id);
        }

        return (new MailMessage)
            ->greeting('Hey ' . $this->vehicleRequest->getApproverName() . ',')
            ->line('You have a new vehicle request awaiting your approval.')
            ->line('Request Number : ' . $this->vehicleRequest->getVehicleRequestNumber())
            ->line('Requester : ' . $this->vehicleRequest->getRequesterName())
            ->line('Travel dates : ' . ($this->vehicleRequest->start_datetime?->format('d M Y h:i A') ?? '-') . ' to ' . ($this->vehicleRequest->end_datetime?->format('d M Y h:i A') ?? '-'))
            ->line('Purpose : ' . ($this->vehicleRequest->purpose_of_travel ?? '-'))
            ->action('View Request', $url);
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

        if ($this->vehicleRequest->vehicle_request_type_id == 1) {
            // Office Vehicle
            $url = route('assign.vehicle.requests.create', $this->vehicleRequest->id);
        } else {
            // Hire Vehicle
            $url = route('approve.vehicle.requests.create', $this->vehicleRequest->id);
        }
        return [
            'vehicle_request_id'    => $this->vehicleRequest->id,
            'link'                  => $url,
            'subject'               => 'Vehicle request ' . $this->vehicleRequest->getVehicleRequestNumber() . ' has been submitted for your approval. Requester : ' . $this->vehicleRequest->getRequesterName(),
        ];
    }
}
