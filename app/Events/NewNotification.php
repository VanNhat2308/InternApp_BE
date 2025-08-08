<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // phát ngay lập tức
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewNotification implements ShouldBroadcastNow
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
    return new PrivateChannel($this->channelName);
    }

    public function broadcastAs()
    {
        return 'notification.received';
    }
}
