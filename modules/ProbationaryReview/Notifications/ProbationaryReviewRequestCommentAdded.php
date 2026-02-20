<?php

namespace Modules\ProbationaryReview\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\ProbationaryReview\Models\ProbationaryReview;

class ProbationaryReviewRequestCommentAdded extends Notification
{
    use Queueable;

    private $probationaryReview;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        ProbationaryReview $probationaryReview
    ) {
        $this->probationaryReview = $probationaryReview;
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
        $url = route('probation.review.detail.requests.recommend', $this->probationaryReview->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line($this->probationaryReview->getEmployeeName() . ' has added comment on Probationary Review request for ' . $this->probationaryReview->getReviewType() . '. Please proceed further required.')
            ->action('View Probationary Review Request ', $url)
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
            'probationary_review_request_id' => $this->probationaryReview->id,
            'link' => route('probation.review.detail.requests.recommend', $this->probationaryReview->id),
            'subject' => $this->probationaryReview->getEmployeeName() . ' has added comment on Probationary Review request for ' . $this->probationaryReview->getReviewType() . '. Please proceed further required.'
        ];
    }
}
