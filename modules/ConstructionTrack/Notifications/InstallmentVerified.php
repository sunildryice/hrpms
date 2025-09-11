<?php

namespace Modules\ConstructionTrack\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\ConstructionTrack\Models\ConstructionInstallment;

class InstallmentVerified extends Notification
{
    use Queueable;

    private $installment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ConstructionInstallment $installment)
    {
        $this->installment = $installment;
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
        $url = route('construction.installment.approve.create', $this->installment->id);

        return (new MailMessage)
                    ->greeting('Hello!')
                    ->line('Construction installment has been submitted for your approval by '.$this->installment->getReviewer().'.')
                    ->action('Approve Installment', $url)
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

    public function toDatabase($notifiable)
    {
        event(new NotificationPushed());

        return [
            'installment_id'    => $this->installment->id,
            'link'              => route('construction.installment.approve.create', $this->installment->id),
            'subject'           => 'Construction installment has been submitted for your approval by '.$this->installment->getReviewer().'.'
        ];
    }
}
