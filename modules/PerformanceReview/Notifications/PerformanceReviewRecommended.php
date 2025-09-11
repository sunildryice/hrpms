<?php

namespace Modules\PerformanceReview\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\PerformanceReview\Models\PerformanceReview;

class PerformanceReviewRecommended extends Notification
{
    use Queueable;

    private $performanceReview;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        PerformanceReview $performanceReview,
    )
    {
        $this->performanceReview = $performanceReview;
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
        $url = route('performance.approve.create', $this->performanceReview->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Performance review ('.$this->performanceReview->getReviewType().') of '.$this->performanceReview->employee->getFullName().' from '.$this->performanceReview->getReviewFromDate().' to '.$this->performanceReview->getReviewToDate().' has been recommended for approval.')
            ->action('View Performance Review', $url)
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
            'performance_review_id' => $this->performanceReview->id,
            'link'                  => route('performance.approve.create', $this->performanceReview->id),
            'subject'               => 'Performance review ('.$this->performanceReview->getReviewType().') of '.$this->performanceReview->employee->getFullName().' from '.$this->performanceReview->getReviewFromDate().' to '.$this->performanceReview->getReviewToDate().' has been recommended for approval.'
        ];
    }

}
