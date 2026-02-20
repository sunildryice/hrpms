<?php

namespace Modules\TrainingRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\TrainingRequest\Models\TrainingReport;

class TrainingReportSubmitted extends Notification
{
    use Queueable;

    private $trainingReport;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        TrainingReport $trainingReport
    ) {
        $this->trainingReport = $trainingReport;
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
        $url = route('approve.training.reports.create', $this->trainingReport->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Training report for training request' . $this->trainingReport->trainingRequest->getTrainingRequestNumber() . ' has been submitted for your approval.')
            ->action('View Training Report ', $url)
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
            'training_report_id' => $this->trainingReport->id,
            'link' => route('approve.training.reports.create', $this->trainingReport->id),
            'subject' => 'Training report for training request ' . $this->trainingReport->trainingRequest->getTrainingRequestNumber() . ' has been submitted.'
        ];
    }
}
