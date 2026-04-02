<?php

namespace Modules\PerformanceReview\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\PerformanceReview\Models\PerformanceReview;

class PerformanceReviewExternalReview extends Notification
{
    use Queueable;

    private $performanceReview;

    public function __construct(PerformanceReview $performanceReview)
    {
        $this->performanceReview = $performanceReview;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $url = route('performance.external-review.show', $this->performanceReview->id);

        return (new MailMessage)
            ->greeting('Hello!')
            ->subject('360 Feedback Request - Performance Review')
            ->line('You have been assigned as External Reviewer for the following performance review:')
            ->line('**Employee:** ' . $this->performanceReview->getEmployeeName())
            ->line('**Review Type:** ' . $this->performanceReview->getReviewType())
            ->line('**Period:** ' . $this->performanceReview->getReviewFromDate() . ' to ' . $this->performanceReview->getReviewToDate())
            ->action('View 360 Feedback', $url)
            ->line('Please provide your valuable feedback as an external reviewer.');
    }

    public function toArray($notifiable)
    {
        return [];
    }

    public function toDatabase($notifiable)
    {
        event(new NotificationPushed());

        return [
            'performance_review_id' => $this->performanceReview->id,
            'type' => 'external_review',
            'link' => route('performance.external-review.show', $this->performanceReview->id),
            'subject' => 'You have been requested to provide 360 Feedback for ' .
                $this->performanceReview->getEmployeeName() .
                ' (' . $this->performanceReview->getReviewType() . ')',
        ];
    }
}