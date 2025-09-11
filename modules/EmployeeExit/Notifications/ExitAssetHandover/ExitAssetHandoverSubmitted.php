<?php

namespace Modules\EmployeeExit\Notifications\ExitAssetHandover;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\EmployeeExit\Models\ExitAssetHandover;

class ExitAssetHandoverSubmitted extends Notification
{
    use Queueable;

    private $exitAssetHandover;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        ExitAssetHandover $exitAssetHandover
    )
    {
        $this->exitAssetHandover = $exitAssetHandover;
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
        $url = route('approve.exit.interview.create', $this->exitAssetHandover->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Exit Asset Handover has been submitted by '.$this->exitAssetHandover->getEmployeeName().' for your approval.')
            ->action('View Exit Asset Handover ', $url)
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
            'exit_interview_id' => $this->exitAssetHandover->id,
            'link'=>route('approve.exit.handover.asset.create', $this->exitAssetHandover->id),
            'subject'=> 'Exit Asset Handover has been submitted by '.$this->exitAssetHandover->getEmployeeName().' for your approval.'
        ];
    }

}
