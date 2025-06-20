<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Jobs\ClassifyTicket;

class TicketController extends Controller
{
     public function index(Request $request)
    {
        $query = Ticket::query();

        // dd($request->all());

        if ($search = $request->input('search')) {
            $query->where('subject', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

       if ($request->filled('category')) {
    if ($request->input('category') === 'unclassified') {
        $query->whereNull('category'); 
    } else {
        $query->where('category', $request->input('category'));
    }
    }


        return response()->json($query->latest()->paginate(10));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

         $data['id'] = (string) Str::ulid(); 

        $ticket = Ticket::create($data);

        return response()->json($ticket, 201);
    }

    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);
        return response()->json($ticket);
    }

    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $data = $request->validate([
            'status' => Rule::in(['open', 'in_progress', 'resolved']),
            'category' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $ticket->update($data);

        return response()->json($ticket);
    }

    public function classify(Ticket $ticket)
    {
        // // Temporary fake data for now
        // $ticket = Ticket::findOrFail($id);

        // $fake = fake();
        // $ticket->update([
        //     'category' => $ticket->category ?? $fake->randomElement(['billing', 'technical', 'account']),
        //     'explanation' => 'Dummy classification used for development.',
        //     'confidence' => $fake->randomFloat(2, 0.6, 0.99),
        // ]);

        // return response()->json($ticket);

        ClassifyTicket::dispatch($ticket);
        return response()->json(['status' => 'queued']);
    }

    public function stats()
    {
        return response()->json([
            'status_counts' => Ticket::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status'),
            'category_counts' => Ticket::selectRaw('category, count(*) as count')->groupBy('category')->pluck('count', 'category'),
        ]);
    }
}
