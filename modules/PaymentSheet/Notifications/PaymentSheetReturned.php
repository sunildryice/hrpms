<?php

namespace Modules\PaymentSheet\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\PaymentSheet\Models\PaymentSheet;

class PaymentSheetReturned extends Notification
{
    use Queueable;

    private $paymentSheet;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        PaymentSheet $paymentSheet
    ) {
        $this->paymentSheet = $paymentSheet;
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
        $url = route('payment.sheets.edit', $this->paymentSheet->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Payment sheet ' . $this->paymentSheet->getPaymentSheetNumber() . ' has been returned.')
            ->action('View Payment Sheet', $url);
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
            'payment_sheet_id' => $this->paymentSheet->id,
            'link' => route('payment.sheets.edit', $this->paymentSheet->id),
            'subject' => 'Payment sheet ' . $this->paymentSheet->getPaymentSheetNumber() . ' has been returned.'
        ];
    }
}
