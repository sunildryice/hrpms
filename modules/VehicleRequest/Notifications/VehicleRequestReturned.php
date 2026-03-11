<?php

namespace Modules\VehicleRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\VehicleRequest\Models\VehicleRequest;

class VehicleRequestReturned extends Notification
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
        $url = route('vehicle.requests.edit', $this->vehicleRequest->id);

        return (new MailMessage)
            ->greeting('Dear ' . $this->vehicleRequest->getRequesterName() . ',')
            ->line('Your vehicle request has been returned for update.')
            ->line('Request Number : ' . $this->vehicleRequest->getVehicleRequestNumber())
            ->line('Travel dates : ' . ($this->vehicleRequest->start_datetime?->format('d M Y h:i A') ?? '-') . ' to ' . ($this->vehicleRequest->end_datetime?->format('d M Y h:i A') ?? '-'))
            ->line('Purpose : ' . ($this->vehicleRequest->purpose_of_travel ?? '-'))
            ->action('Review Request', $url);
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
            'vehicle_request_id'    => $this->vehicleRequest->id,
            'link'                  => route('vehicle.requests.edit', $this->vehicleRequest->id),
            'subject'               => 'Your vehicle request ' . $this->vehicleRequest->getVehicleRequestNumber() . ' has been returned for update.'
        ];
    }
}
