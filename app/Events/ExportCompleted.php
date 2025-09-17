<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExportCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('export.pembayaran');
    }

    public function broadcastAs()
    {
        return 'export.completed';
    }
}
