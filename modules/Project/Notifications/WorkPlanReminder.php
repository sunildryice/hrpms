<?php

namespace Modules\Project\Notifications;

use App\Events\NotificationPushed;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkPlanReminder extends Notification
{
    use Queueable;

    private Carbon $weekStart;
    private Carbon $weekEnd;

    public function __construct(Carbon $weekStart, Carbon $weekEnd)
    {
        $this->weekStart = $weekStart;
        $this->weekEnd = $weekEnd;
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
        $weekLabel = $this->weekStart->format('M j, Y') . ' - ' . $this->weekEnd->format('M j, Y');
        $url = route('work-plan.index');
        return (new MailMessage)
            ->greeting('Dear ' . ($notifiable->full_name ?? 'Employee') . ',')
            ->subject('Weekly Work Plan Reminder')
            ->line('This is your weekly reminder to update your work plan for the week of ' . $weekLabel . '.')
            ->line('Please update your weekly work plan in the system.')
            ->action('View Work Plans', $url);
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
            'link' => route('work-plan.index'),
            'alternate_link' => route('work-plan.index'),
            'subject' => 'Weekly Work Plan Reminder',
            'message' => 'This is your weekly reminder to update your work plan for the week of ' . $this->weekStart->format('M j, Y') . ' - ' . $this->weekEnd->format('M j, Y') . '.',
        ];
    }
}