<?php

namespace Modules\Project\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Project\Models\TimeSheet;

class TimeSheetSubmitted extends Notification
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
        $url = route('approve.monthly-timesheet.create', $this->timeSheet->id);
        return (new MailMessage)
            ->greeting('Hey ' . ($this->timeSheet->approver->getFullName() ?? '-') . ',')
            ->line('You have a new timesheet awaiting your review.')
            ->line('Employee: ' . ($this->timeSheet->requester->getFullName() ?? '-'))
            ->line('Period: ' . ($this->timeSheet->month_name ?? $this->timeSheet->month) . ' ' . $this->timeSheet->year)
            ->line('Dates: ' . ($this->timeSheet->start_date ? $this->timeSheet->start_date->format('d M Y') : '-') . ' to ' . ($this->timeSheet->end_date ? $this->timeSheet->end_date->format('d M Y') : '-'))
            ->action('Review Timesheet', $url);
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
        $requesterName = $this->timeSheet->requester->getFullName() ?? '';
        event(new NotificationPushed());
        return [
            'timesheet_id' => $this->timeSheet->id,
            'link' => route('approve.monthly-timesheet.create', $this->timeSheet->id),
            'alternate_link' => route('approve.monthly-timesheet.create', $this->timeSheet->id),
            'subject'          => "Timesheet for {$monthYear} has been submitted. Requester: {$requesterName}.",
        ];
    }
}
