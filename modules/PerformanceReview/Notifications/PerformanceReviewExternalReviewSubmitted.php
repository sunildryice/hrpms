<?php

namespace Modules\PerformanceReview\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\PerformanceReview\Models\PerformanceReview;

class PerformanceReviewExternalReviewSubmitted extends Notification
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
        $url = $this->getCorrectUrl($notifiable);

        return (new MailMessage)
            ->greeting('Hello!')
            ->line('The 360 Feedback for **' . $this->performanceReview->getEmployeeName() . '**')
            ->line('(' . $this->performanceReview->getReviewType() . ') has been submitted by the External Reviewer.')
            ->line('The review is now closed.')
            ->action('View Performance Review', $url);
    }

    public function toDatabase($notifiable)
    {
        event(new NotificationPushed());

        return [
            'performance_review_id' => $this->performanceReview->id,
            'link' => $this->getCorrectUrl($notifiable),
            'subject' => '360 Feedback has been submitted and review is closed for ' .
                $this->performanceReview->getEmployeeName(),
        ];
    }
    private function getCorrectUrl($notifiable)
    {
        if ($notifiable->id === $this->performanceReview->requester_id) {
            return route('performance.employee.show', $this->performanceReview->id);
        }

        return route('performance.show', $this->performanceReview->id);
    }
}