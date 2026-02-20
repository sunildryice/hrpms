<?php

namespace Modules\EmployeeRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\EmployeeRequest\Models\EmployeeRequest;

class EmployeeRequestSubmitted extends Notification
{
    use Queueable;

    private $employeeRequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        EmployeeRequest $employeeRequest
    ) {
        $this->employeeRequest = $employeeRequest;
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
        $url = route('review.employee.requests.create', $this->employeeRequest->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Employee request has been submitted for your review.')
            ->action('View Employee Request ', $url)
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
            'employee_requisition_id' => $this->employeeRequest->id,
            'link' => route('review.employee.requests.create', $this->employeeRequest->id),
            'subject' => 'Employee request has been submitted.'
        ];
    }
}
