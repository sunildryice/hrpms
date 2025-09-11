<?php

namespace Modules\PaymentSheet\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\PaymentSheet\Models\PaymentSheet;

class PaymentSheetRecommended extends Notification
{
    use Queueable;

    private $paymentSheet;
    private $message;
    private $url;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        PaymentSheet $paymentSheet
    )
    {
        $this->paymentSheet = $paymentSheet;

        if ($paymentSheet->status_id == config('constant.RECOMMENDED_STATUS')) {
            $this->message = 'Payment sheet '.$this->paymentSheet->getPaymentSheetNumber().' has been recommended for your review. Requester : '.$this->paymentSheet->getRequesterName();
            $this->url = route('review.recommended.payment.sheets.create', $this->paymentSheet->id);
        }

        if ($paymentSheet->status_id == config('constant.RECOMMENDED2_STATUS')) {
            $this->message = 'Payment sheet '.$this->paymentSheet->getPaymentSheetNumber().' has been recommended for your approval. Requester : '.$this->paymentSheet->getRequesterName();
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
            'payment_sheet_id'  => $this->paymentSheet->id,
            'link'              => $this->url,
            'subject'           => $this->message
        ];
    }

}
