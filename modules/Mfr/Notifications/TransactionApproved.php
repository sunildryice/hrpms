<?php

namespace Modules\Mfr\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Mfr\Models\Transaction;

class TransactionApproved extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        private Transaction $transaction,
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [ 'database'];
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
            'transaction_id' => $this->transaction->id,
            'link' => route('mfr.transaction.show', [$this->transaction->id]),
            'subject' => "{$this->transaction->getType()} for ".$this->transaction->transaction_date->format('F').' has been approved by '. $this->transaction->getApprover(),
        ];
    }
}
