<?php

namespace Modules\TransportationBill\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\TransportationBill\Models\TransportationBill;

class TransportationBillSubmitted extends Notification
{
    use Queueable;

    private $transportationBill;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        TransportationBill $transportationBill
    )
    {
        $this->transportationBill = $transportationBill;
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
        $url = route('approve.transportation.bills.create', $this->transportationBill->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Transportation bill '.$this->transportationBill->getTransportationBillNumber().' has been submitted for your approval.')
            ->action('View Transportation Bill ', $url)
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
            'transportation_bill_id' => $this->transportationBill->id,
            'link'=>route('approve.transportation.bills.create', $this->transportationBill->id),
            'subject'=> 'Transportation bill '.$this->transportationBill->getTransportationBillNumber().' has been submitted.'
        ];
    }

}
