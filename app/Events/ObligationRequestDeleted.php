<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ObligationRequestDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $obligationRequestId;
    public $obrNumber;

    /**
     * Create a new event instance.
     */
    public function __construct(int $obligationRequestId, string $obrNumber)
    {
        $this->obligationRequestId = $obligationRequestId;
        $this->obrNumber = $obrNumber;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('obligation-requests'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'obligation.deleted';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->obligationRequestId,
            'obr_number' => $this->obrNumber,
        ];
    }
}
