<?php

namespace App\Events;

use App\Models\ObligationRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ObligationRequestCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $obligationRequest;

    /**
     * Create a new event instance.
     */
    public function __construct(ObligationRequest $obligationRequest)
    {
        $this->obligationRequest = $obligationRequest;
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
        return 'obligation.created';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->obligationRequest->id,
            'obr_number' => $this->obligationRequest->obr_number,
            'department_name' => $this->obligationRequest->department->name,
            'claimant_payee_name' => $this->obligationRequest->claimantPayee->name,
            'obligation_date' => $this->obligationRequest->obligation_date->format('M d, Y'),
            'total_amount' => $this->obligationRequest->total_amount,
            'status' => $this->obligationRequest->status,
            'created_at' => $this->obligationRequest->created_at->toIso8601String(),
        ];
    }
}
