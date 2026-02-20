<?php

namespace Modules\EmployeeAttendance\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\EmployeeAttendance\Models\Attendance;


class AttendanceSubmitted extends Notification
{
    use Queueable;

    private $attendance;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        Attendance $attendance,
    ) {
        $this->attendance = $attendance;
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
        $url = route('attendance.review.create', $this->attendance->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Attendance for ' . $this->attendance->getYearMonth() . ' has been submitted for your verification by ' . $this->attendance->getRequester())
            ->action('View Attendance', $url)
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
            'attendance_id' => $this->attendance->id,
            'link'          => route('attendance.review.create', $this->attendance->id),
            'subject'       => 'Attendance for ' . $this->attendance->getYearMonth() . ' has been submitted for your verification by ' . $this->attendance->getRequester()
        ];
    }
}
