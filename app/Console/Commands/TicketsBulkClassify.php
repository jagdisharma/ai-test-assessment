<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ticket;
use App\Jobs\ClassifyTicket;


class TicketsBulkClassify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:bulk-classify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue classification jobs for unclassified tickets';

    /**
     * Execute the console command.
     */
    
    public function handle()
    {
        $tickets = Ticket::whereNull('category')->get();

        foreach ($tickets as $ticket) {
            ClassifyTicket::dispatch($ticket);
        }

        $this->info("Queued classification jobs for {$tickets->count()} tickets.");
    }
}
