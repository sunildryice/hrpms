<?php

namespace Modules\EmployeeExit\Notifications\ExitAssetHandover;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\EmployeeExit\Models\ExitAssetHandover;

class ExitAssetHandoverReturned extends Notification
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
    ) {
        $this->$exitAssetHandover = $exitAssetHandover;
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
        $url = route('exit.employee.handover.asset.edit');
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Your Exit Asset Handover has been returned.')
            ->action('View Exit Asset Handover ', $url)
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
            'exit_asset_handover_id' => $this->exitAssetHandover->id,
            'link' => route('exit.employee.handover.asset.edit'),
            'subject' => 'Your Exit Asset Handover has been returned.'
        ];
    }
}
