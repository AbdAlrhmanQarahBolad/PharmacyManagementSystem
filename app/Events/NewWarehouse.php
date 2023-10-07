<?php

namespace App\Events;

use App\Models\Warehouse;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewWarehouse implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $warehouse;
    /**
     * Create a new event instance.
     */
    public function __construct(Warehouse $warehouse)
    {
        $this->$warehouse = $warehouse;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('1'),
        ];
    }
    public function broadcastWith()
    {
        return  [
            'message' => 'مرحى! اشتغلت'
        ];
    }
}
