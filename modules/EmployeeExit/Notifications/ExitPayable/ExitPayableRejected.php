<?php

namespace Modules\EmployeeExit\Notifications\ExitPayable;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\EmployeeExit\Models\EmployeeExitPayable;

class ExitPayableRejected extends Notification
{
    use Queueable;

    private $employeeExitPayable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        EmployeeExitPayable $employeeExitPayable
    )
    {
        $this->employeeExitPayable = $employeeExitPayable;
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
        $url = route('exit.payable.show', $this->employeeExitPayable->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Employee Exit Payable for '.$this->employeeExitPayable->getEmployeeName().' has been rejected.')
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
        return [
            'exit_payable_id' => $this->employeeExitPayable->id,
            'link'=>route('exit.payable.show', $this->employeeExitPayable->id),
            'subject'=> 'Employee Exit Payable for '.$this->employeeExitPayable->getEmployeeName().' has been rejected.'
        ];
    }

}
