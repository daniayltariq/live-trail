<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CheckInStatus implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Properties for attendance data and check-in type
    public $att;
    public $check_in_type;

    /**
    * Create a new event instance.
    * @param  mixed  $att - Attendance data to broadcast
    * @param  string  $check_in_type - Type of check-in, e.g., "in" or "out"
    */
    public function __construct( $att, $check_in_type)
    {
        // Initialize properties with provided data
        $this->att = $att;
        $this->check_in_type = $check_in_type;
    }

    /**
    * Data to be broadcast with the event.
    *
    * @return array - Array of attendance data with formatted time values
    */
    public function broadcastWith(): array
    {
        return
        [
            'id' => $this->att->id,
            'name' => $this->att->user->name,
            // Format time_in with date and 12-hour time format if available
            'time_in' => $this->att->time_in ? $this->att->date ." ". \Carbon\Carbon::createFromFormat('H:i:s', $this->att->time_in)->format('h:i A') : null,
            // Format time_out with date and 12-hour time format if available
            'time_out' => $this->att->time_out ? $this->att->date ." ". \Carbon\Carbon::createFromFormat('H:i:s', $this->att->time_out)->format('h:i A') : null,
            'check_in_type' => $this->check_in_type,
        ];
    }

    /**
    * Custom event name to broadcast.
    *
    * @return string - Custom name for the broadcast event
    */
    public function broadcastAs(): string
    {
        return 'check';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('owner'),
        ];
    }
}
