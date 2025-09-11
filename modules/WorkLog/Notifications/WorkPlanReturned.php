<?php

namespace Modules\WorkLog\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\WorkLog\Models\WorkPlan;

class WorkPlanReturned extends Notification
{
    use Queueable;

    private $workPlan;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        WorkPlan $workPlan
    )
    {
        $this->workPlan = $workPlan;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = route('daily.work.logs.index', $this->workPlan->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Work Log for '.$this->workPlan->getYearMonth().' has been returned by '.$this->workPlan->getApprover())
            ->action('View Work log', $url)
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
            'work_plan_id' => $this->workPlan->id,
            'link'=>route('daily.work.logs.index', $this->workPlan->id),
            'subject'=> 'Work Log for '.$this->workPlan->getYearMonth().' has been returned by '.$this->workPlan->getApprover()
        ];
    }

}
