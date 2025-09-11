<?php

namespace Modules\EmployeeExit\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\EmployeeExit\Models\ExitHandOverNote;

class ExitHandoverNoteSubmitted extends Notification
{
    use Queueable;

    private $exitHandOverNote;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        ExitHandOverNote $exitHandOverNote
    )
    {
        $this->exitHandOverNote = $exitHandOverNote;
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
        $url = route('approve.exit.handover.note.create', $this->exitHandOverNote->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Exit Handover Note has been submitted by '.$this->exitHandOverNote->getEmployeeName().' for your approval.')
            ->action('View Exit Handover Note ', $url)
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
            'handover_id' => $this->exitHandOverNote->id,
            'link'=>route('approve.exit.handover.note.create', $this->exitHandOverNote->id),
            'subject'=> 'Exit Handover Note has been submitted by '.$this->exitHandOverNote->getEmployeeName().' for your approval.'
        ];
    }

}
