<?php

namespace Modules\EmployeeExit\Notifications\ExitInterview;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\EmployeeExit\Models\ExitInterview;

class ExitInterviewRecommended extends Notification
{
    use Queueable;

    private $exitInterview;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        ExitInterview $exitInterview
    )
    {
        $this->exitInterview = $exitInterview;
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
        $url = route('approve.exit.interview.create', $this->exitInterview->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Exit Interview of '.$this->exitInterview->getEmployeeName().' has been recommended for your approval.')
            ->action('View Exit Interview ', $url)
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
            'exit_interview_id' => $this->exitInterview->id,
            'link'=>route('approve.exit.interview.create', $this->exitInterview->id),
            'subject'=> 'Exit Interview of '.$this->exitInterview->getEmployeeName().' has been recommended for your approval.'
        ];
    }

}
