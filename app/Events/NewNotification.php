<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

use App\Models\Item;


class NewNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;
    public $item;

    public function __construct($notification, $item = null)
    {
        $this->notification = $notification;
        $this->item = $item;
    }   

    public function broadcastOn()
    {
        Log::info('newNotification: ', ['newNotification' => $this->notification->id_user]);

        return'lbaw2366-' . $this->notification->id_user;
    }

    public function broadcastAs() {
        return 'new-notification';
    }
}
