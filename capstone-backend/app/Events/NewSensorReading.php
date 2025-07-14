<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class NewSensorReading implements ShouldBroadcastNow
{
    use SerializesModels, InteractsWithSockets;

    public $reading;

    public function __construct($reading)
    {
        $this->reading = $reading;
    }

    public function broadcastOn()
    {
        return new Channel('sensor-readings');
    }

    public function broadcastAs()
    {
        return 'new-reading';
    }
}
