<?php

namespace Modules\Grn\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Grn\Models\Grn;
use Modules\Inventory\Models\InventoryItem;

class InventoryPushed extends Notification
{
    use Queueable;

    private $inventory;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        InventoryItem $inventory
    ) {
        $this->inventory = $inventory;
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
        return [
            'inventory_id' => $this->inventory->id,
            'link' => route('inventories.show', $this->inventory->id),
            'subject' => 'Grn item, ' . $this->inventory->getItemName() . ', has been pushed to the inventory.'
        ];
    }
}
