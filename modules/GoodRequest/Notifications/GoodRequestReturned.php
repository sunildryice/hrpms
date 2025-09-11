<?php

namespace Modules\GoodRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\GoodRequest\Models\GoodRequest;

class GoodRequestReturned extends Notification
{
    use Queueable;

    private $goodRequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        GoodRequest $goodRequest
    )
    {
        $this->goodRequest = $goodRequest;
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
        $url = route('good.requests.edit', $this->goodRequest->id);
        return (new MailMessage)
                ->greeting('Hello!')
                ->line('Good request '.$this->goodRequest->getGoodRequestNumber().' has been returned.')
                ->action('View Good Request', $url)
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
            'good_request_id' => $this->goodRequest->id,
            'link'=>route('good.requests.edit', $this->goodRequest->id),
            'subject'=> 'Good request '.$this->goodRequest->getGoodRequestNumber().' has been returned.'
        ];
    }

}
