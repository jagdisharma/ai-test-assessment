<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Ticket;
use App\Services\TicketClassifier;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClassifyTicket implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Ticket $ticket;

    /**
     * Create a new job instance.
     */
     public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $classifier = new TicketClassifier();
            $result = $classifier->classify($this->ticket->subject, $this->ticket->body);

            if (!isset($result['category']) || !isset($result['confidence'])) {
                throw new \Exception('Invalid classification result format');
            }

            if (is_null($this->ticket->category)) {
                $this->ticket->category = $result['category'];
            }

            $this->ticket->explanation = $result['explanation'] ?? 'No explanation provided';
            $this->ticket->confidence = $result['confidence'];

            $this->ticket->save();
            
            \Log::info('Ticket classified successfully', [
                'ticket_id' => $this->ticket->id,
                'category' => $this->ticket->category,
                'confidence' => $this->ticket->confidence
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Ticket classification failed', [
                'ticket_id' => $this->ticket->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }
}
