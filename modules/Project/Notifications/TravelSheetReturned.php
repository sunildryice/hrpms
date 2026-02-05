<?php

namespace Modules\Project\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Project\Models\TimeSheet;

class TravelSheetReturned extends Notification
{
    use Queueable;

    private $timeSheet;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        TimeSheet $timeSheet
    ) {
        $this->timeSheet = $timeSheet;
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
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
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
        $monthYear = $this->timeSheet->month . ' ' . $this->timeSheet->year;
        event(new NotificationPushed());
        return [
            'travel_report_id' => $this->timeSheet->id,
            'link' => route('monthly-timesheet.show', $this->timeSheet->id),
            'alternate_link' => route('monthly-timesheet.show', $this->timeSheet->id),
            'subject' => "Timesheet for {$monthYear} has been returned",
        ];
    }

}
