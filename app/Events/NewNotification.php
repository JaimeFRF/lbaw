<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    // Here you create the message to be sent when the event is triggered.
    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    // You should specify the name of the channel created in Pusher.
    public function broadcastOn()
    {
        return'lbaw2366';
    }

    public function broadcastAs() {
        return 'new-notification';
    }
}
