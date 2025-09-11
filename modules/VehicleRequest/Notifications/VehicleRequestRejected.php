<?php

namespace Modules\VehicleRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\VehicleRequest\Models\VehicleRequest;

class VehicleRequestRejected extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        VehicleRequest $vehicleRequest
    )
    {
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
        $url = route('vehicle.requests.show', $this->vehicleRequest->id);

        return (new MailMessage)
        ->greeting('Hello!')
        ->line('Vehicle request '.$this->vehicleRequest->getVehicleRequestNumber().' has been rejected.')
        ->action('View Request', $url)
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
            'vehicle_request_id' => $this->vehicleRequest->id,
            'link'=>route('vehicle.requests.show', $this->vehicleRequest->id),
            'subject'=> 'Vehicle request '.$this->vehicleRequest->getVehicleRequestNumber().' has been rejected.'
        ];
    }

}
