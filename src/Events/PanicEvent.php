<?php

namespace Codificar\Panic\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Codificar\Panic\Models\Panic;

class PanicEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $request;
    public $request_id;
    public $panic_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(panic $request, int $request_id)
    {
        $this->request = $request;
        $this->request_id = $request_id;
        $this->panic_id = Panic::where('request_id', $request_id);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('panic', $this->panic_id);
    }

    /**
     * This function creates the parameters that will be passed as the message of the event to be listened to later
     */
    public function broadcastWith()
    {
        $history = $this->request->history;
        $requestId = $this->request->request_id;
        $id = $this->request->id;
        $createdAt = $this->request->created_at;

        return [
            'id' => $id,
            'request_id' => $requestId,
            'history' => $history,
            'created_at' => $createdAt,
        ];
    }

    /**
     * This function creates the title of the event that will be broadcast
     * @return string 
     *  */
    public function broadcastAs()
    {
        return 'panicEvent';
    }
}
