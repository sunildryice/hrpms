<?php

namespace Modules\AdvanceRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\AdvanceRequest\Models\Settlement;

class AdvanceSettlementReturned extends Notification
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
    )
    {
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
        $url = route('advance.settlement.edit', [$this->advanceSettlement->id]);
        return (new MailMessage)
        ->greeting('Hello !')
        ->line('Advance settlement for advance request '.$this->advanceSettlement->advanceRequest->getAdvanceRequestNumber().' has been returned.')
        ->action('View Advance Settlement', $url)
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
            'advance_settlement_id' => $this->advanceSettlement->id,
            'link'=>route('advance.settlement.edit', [$this->advanceSettlement->id]),
            'subject'=> 'Advance settlement for advance request '.$this->advanceSettlement->advanceRequest->getAdvanceRequestNumber().' has been returned.'
        ];
    }

}
