<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewNotification implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $notification;
    public $channelName;
    public $unreadCount;

    public function __construct(Notification $notification, $channelName, $unreadCount)
    {
        Log::info("NewNotification event fired", [
        'channel' => $channelName,
        'unreadCount' => $unreadCount
    ]);
        $this->notification = $notification;
        $this->channelName = $channelName; 
        $this->unreadCount = $unreadCount;

    }

    public function broadcastOn()
    {
    return new Channel($this->channelName);
    }

    public function broadcastAs()
    {
        return 'notification.received';
    }
}
