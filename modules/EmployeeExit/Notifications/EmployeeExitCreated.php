<?php

namespace Modules\EmployeeExit\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\EmployeeExit\Models\ExitHandOverNote;

class EmployeeExitCreated extends Notification
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
    ) {
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
        $url = route('exit.employee.handover.note.edit');

        return (new MailMessage)
            ->greeting('Dear ' . $this->exitHandOverNote->employee?->getFullName() . ',')
            ->line('Employee exit has been created for you. Please do the further required actions.')
            ->action('View Employee Exit', $url);
    }
    /**
     * Get the array representation of the notification.re
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
            'employee_exit_id' => $this->exitHandOverNote->id,
            'link' => route('exit.employee.handover.note.edit'),
            'subject' => 'Employee exit has been created for you. Please do further required actions.'
        ];
    }
}
