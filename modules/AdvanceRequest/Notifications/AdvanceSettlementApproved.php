<?php

namespace Modules\AdvanceRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\AdvanceRequest\Models\Settlement;

class AdvanceSettlementApproved extends Notification
{
    use Queueable;

    private $advanceSettlement;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        Settlement $advanceSettlement
    ) {
        $this->advanceSettlement = $advanceSettlement;
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
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'));
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
            'advance_settlement_id' => $this->advanceSettlement->id,
            'link' => route('advance.settlement.show', $this->advanceSettlement->id),
            'subject' => 'Advance settlement for advance request ' . $this->advanceSettlement->advanceRequest->getAdvanceRequestNumber() . ' has been approved.'
        ];
    }
}
