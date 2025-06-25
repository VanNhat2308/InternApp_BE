<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewMessage implements ShouldBroadcast
{
    use SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        

        $this->message = $message->load('conversation');
    }

    public function broadcastOn()
    {
        return new Channel('chat.' . $this->message->to_id);
    }

    public function broadcastWith()
    {
        return ['message' => $this->message];
    }
}
