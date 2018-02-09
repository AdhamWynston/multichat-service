<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RequestReceived extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    /**
     * @var string $requestId
     */
    private $requestId;

    /**
     * Create a new notification instance.
     */
    public function __construct($requestId)
    {
        $this->requestId = $requestId;
    }

    /**
     * Get the channel or channels to broadcast on.
     *
     * @return string
     */
    public function broadcastOn()
    {
        return 'request-received';
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->requestId,
        ];
    }
}