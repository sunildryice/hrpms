<?php
namespace App\Repositories;

use App\Models\Notification;

class NotificationRepository extends Repository
{
    public function __construct(Notification $notification)
    {
        $this->model = $notification;
    }

    public function getNotifications()
    {
        return auth()->user()->notifications->take(3);
    }
}
