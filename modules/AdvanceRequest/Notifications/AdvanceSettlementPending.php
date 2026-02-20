<?php

namespace Modules\AdvanceRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\AdvanceRequest\Models\AdvanceRequest;

class AdvanceSettlementPending extends Notification
{
    use Queueable;

    private $advanceRequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        AdvanceRequest $advanceRequest
    ) {
        $this->advanceRequest = $advanceRequest;
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
        $url = route('advance.settlement.create', $this->advanceRequest->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Advance settlement for advance request ' . $this->advanceRequest->getAdvanceRequestNumber() . ' is pending for submission.')
            ->action('Submit Settlement', $url);
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
            'advance_settlement_id' => $this->advanceRequest->id,
            'link' => route('advance.settlement.create', $this->advanceRequest->id),
            'subject' => 'Advance settlement for advance request ' . $this->advanceRequest->getAdvanceRequestNumber() . ' is pending for submission.'
        ];
    }
}
