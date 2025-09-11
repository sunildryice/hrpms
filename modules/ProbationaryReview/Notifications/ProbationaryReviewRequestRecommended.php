<?php

namespace Modules\ProbationaryReview\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\ProbationaryReview\Models\ProbationaryReview;

class ProbationaryReviewRequestRecommended extends Notification
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
    )
    {
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
        $url = route('employeeProbation.review.detail.requests.create', $this->probationaryReview->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Probationary Review request for '.$this->probationaryReview->getReviewType().' has been recommended. Please add your comment.')
            ->action('View Probationary Review Request ', $url)
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
            'probationary_review_request_id' => $this->probationaryReview->id,
            'link'=>route('employeeProbation.review.detail.requests.create', $this->probationaryReview->id),
            'subject'=> 'Probationary Review request for '.$this->probationaryReview->getReviewType().' has been sent for your feedback.'
        ];
    }

}
