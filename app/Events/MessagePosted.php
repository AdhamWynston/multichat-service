<?php

namespace App\Events;

use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class MessagePosted implements ShouldBroadcast
{
    use SerializesModels;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->message = "Oi, tudo bem?";
        $this->user = "Adham";
    }

    public function broadcastOn(){
        return new PrivateChannel('multichat');
    }
}
