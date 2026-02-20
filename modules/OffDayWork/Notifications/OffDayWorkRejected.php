<?php

namespace Modules\OffDayWork\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

use Illuminate\Notifications\Notification;
use Modules\OffDayWork\Models\OffDayWork;

class OffDayWorkRejected extends Notification
{
    use Queueable;

    protected $offDayWork;
    public function __construct(
        OffDayWork $offDayWork
    ) {
        $this->offDayWork = $offDayWork;
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
        $url = route('off.day.work.show', $this->offDayWork->id);
        $url = route('off.day.work.show', $this->offDayWork->id);
        return (new MailMessage)
            ->greeting('Hey ' . ($this->offDayWork->requester->getFullName() ?? '-') . ',')
            ->line('Your off day work request has been rejected.')
            ->line('Request Number: ' . ($this->offDayWork->getRequestId() ?? $this->offDayWork->id))
            ->line('Work Date: ' . ($this->offDayWork->getOffDayWorkDate() ?? '-'))
            ->line('Rejected by: ' . (auth()->user()->full_name ?? '-'))
            ->action('View off day work request', $url);
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
            'off_day_work_id' => $this->offDayWork->id,
            'link' => route('off.day.work.show', $this->offDayWork->id),
            'alternate_link' => route('off.day.work.show', $this->offDayWork->id),
            'subject' => 'Off day work request ' . $this->offDayWork->id . ' has been rejected. Requester : ' . $this->offDayWork->requester->getFullName(),
        ];
    }
}
