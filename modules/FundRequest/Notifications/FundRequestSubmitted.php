<?php

namespace Modules\FundRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\FundRequest\Models\FundRequest;

class FundRequestSubmitted extends Notification
{
    use Queueable;

    private $fundRequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        FundRequest $fundRequest
    ) {
        $this->fundRequest = $fundRequest;
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
        $url = route('check.fund.requests.create', $this->fundRequest->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Fund request ' . $this->fundRequest->getFundRequestNumber() . ' has been submitted for your review.')
            ->action('View Fund Request ', $url)
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
        return [
            'fund_request_id' => $this->fundRequest->id,
            'link' => route('check.fund.requests.create', $this->fundRequest->id),
            'subject' => 'Fund request ' . $this->fundRequest->getFundRequestNumber() . ' has been submitted for you verification.'
        ];
    }
}
