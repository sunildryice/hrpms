<?php

namespace Modules\TravelRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\TravelRequest\Models\TravelReport;

class TravelReportApproved extends Notification
{
    use Queueable;

    private $travelReport;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        TravelReport $travelReport
    ) {
        $this->travelReport = $travelReport;
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
            ->action('Notification Action', url('/'));
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
            'travel_report_id' => $this->travelReport->id,
            'link' => route('travel.reports.show', $this->travelReport->id),
            'alternate_link' => route('travel.reports.show', $this->travelReport->id),
            'subject' => 'Travel report for travel request ' . $this->travelReport->travelRequest->getTravelRequestNumber() . ' has been approved.'
        ];
    }
}
