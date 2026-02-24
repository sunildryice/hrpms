<?php

namespace Modules\TravelRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\TravelRequest\Models\LocalTravel;

class LocalTravelSubmitted extends Notification
{
    use Queueable;

    private $localTravel;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        LocalTravel $localTravel
    ) {
        $this->localTravel = $localTravel;
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
        $url = route('approve.local.travel.reimbursements.create', $this->localTravel->id);
        return (new MailMessage)
            ->greeting('Hey ' . ($this->localTravel->getApproverName() ?? '-') . ',')
            ->line('You have a new local travel reimbursement request awaiting your approval.')
            ->line('Employee: ' . ($this->localTravel->getRequesterName() ?? '-'))
            ->line('Reimbursement Number: ' . ($this->localTravel->getLocalTravelNumber() ?? '-'))
            ->line('Period: ' . ($this->localTravel->start_date ? $this->localTravel->start_date->format('d M Y') : '-') . ' to ' . ($this->localTravel->end_date ? $this->localTravel->end_date->format('d M Y') : '-'))
            ->action('View Local travel reimbursement', $url);
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
            'local_travel_reimbursement_id' => $this->localTravel->id,
            'link' => route('approve.local.travel.reimbursements.create', $this->localTravel->id),
            'alternate_link' => route('local.travel.reimbursements.show', $this->localTravel->id),
            'subject' => 'Local travel reimbursement ' . $this->localTravel->getLocalTravelNumber() . ' has been submitted. Requester : ' . $this->localTravel->getRequesterName() . '.',
        ];
    }
}
