<?php

namespace Modules\Memo\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Memo\Models\Memo;

class MemoRecommended extends Notification
{
    use Queueable;

    private $memo;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        Memo $memo
    ) {
        $this->memo = $memo;
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
        $url = route('memo.index');
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Memo ' . $this->memo->getMemoNumber() . ' has been recommended for approval.')
            ->action('View Memo ', $url)
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
            'memo_id' => $this->memo->id,
            'link' => route('memo.index'),
            'subject' => 'Memo ' . $this->memo->getMemoNumber() . ' has been recommended for approval.'
        ];
    }
}
