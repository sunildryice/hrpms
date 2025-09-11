<?php

namespace Modules\EmployeeExit\Notifications\ExitPayable;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\EmployeeExit\Models\EmployeeExitPayable;
use Modules\EmployeeExit\Models\ExitHandOverNote;

class ExitPayableSettlement extends Notification
{
    use Queueable;

    private $employeeExitPayable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        ExitHandOverNote $exitHandoverNote
    )
    {
        $this->exitHandoverNote = $exitHandoverNote;
        $this->employeeExitPayable = $exitHandoverNote->employeeExitPayable;
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
        $url = route('exit.payable.index');
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Employee Exit Payable for '.$this->employeeExitPayable->getEmployeeName().' has been submitted.')
            ->action('View Employee Exit Payable ', $url)
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
        $url = route('exit.payable.index');
        return [
            'exit_payable_id'   => $this->employeeExitPayable->id,
            'link'              => $url,
            'subject'           => 'Employee Exit Payable for '.$this->employeeExitPayable->getEmployeeName().' has been submitted.'
        ];
    }

}
