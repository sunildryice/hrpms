<?php

namespace Modules\PaymentSheet\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\PaymentSheet\Models\PaymentSheet;

class PaymentSheetVerified extends Notification
{
    use Queueable;

    private $message;
    private $paymentSheet;
    private $url;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        PaymentSheet $paymentSheet
    ) {
        $this->paymentSheet = $paymentSheet;

        if ($paymentSheet->status_id == config('constant.VERIFIED_STATUS')) {
            $this->message = 'Payment Sheet ' . $this->paymentSheet->getPaymentSheetNumber() . ' submitted by ' . $this->paymentSheet->getRequesterName() . ' has been verified and forwarded for your approval.';
            $this->url = route('approve.payment.sheets.create', $this->paymentSheet->id);
        }

        if ($paymentSheet->status_id == config('constant.RECOMMENDED2_STATUS')) {
            $this->message = 'Payment Sheet ' . $this->paymentSheet->getPaymentSheetNumber() . ' submitted by ' . $this->paymentSheet->getRequesterName() . ' has been verified and recommended for your approval.';
            $this->url = route('approve.recommended.payment.sheets.create', $this->paymentSheet->id);
        }
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
            ->greeting('Hello!')
            ->line($this->message)
            ->action('View Payment Sheet ', $this->url)
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
            'payment_sheet_id'  => $this->paymentSheet->id,
            'link'              => $this->url,
            'subject'           => $this->message
        ];
    }
}
