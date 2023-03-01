<?php

namespace Codificar\Panic\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class EventNewPanicMessageNotification implements ShouldBroadcast {
	use InteractsWithSockets, SerializesModels;

	private $message;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct() {}

	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return Channel|array
	 */
	public function broadcastOn() {
		return new Channel('chatPanicMessageAdminNotification');
	}

	/**
	 * Get the data to broadcast.
	 *
	 * @return array
	 */
	public function broadcastWith() {
		return ['success' => true];
	}

	/**
	 * The event's broadcast name.
	 *
	 * @return string
	 */
	public function broadcastAs() {
		return 'newPanicMessage';
	}

}
