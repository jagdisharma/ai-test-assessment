<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Jobs\ClassifyTicket;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        try {
            ClassifyTicket::dispatch($ticket);
            return response()->json(['status' => 'queued', 'message' => 'Classification job queued successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to queue classification job'], 500);
        }
    }

    public function checkClassificationStatus($id)
    {
        $ticket = Ticket::findOrFail($id);
        return response()->json([
            'id' => $ticket->id,
            'category' => $ticket->category,
            'confidence' => $ticket->confidence,
            'explanation' => $ticket->explanation,
            'is_classified' => !is_null($ticket->category)
        ]);
    }

    public function stats()
    {
        return response()->json([
            'status_counts' => Ticket::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status'),
            'category_counts' => Ticket::selectRaw('category, count(*) as count')->groupBy('category')->pluck('count', 'category'),
            'unclassified_count' => Ticket::whereNull('category')->count(),
        ]);
    }

    public function bulkClassify()
    {
        try {
            $tickets = Ticket::whereNull('category')->get();

            if ($tickets->isEmpty()) {
                return response()->json([
                    'status' => 'no_tickets',
                    'message' => 'No unclassified tickets found'
                ]);
            }

            foreach ($tickets as $ticket) {
                ClassifyTicket::dispatch($ticket);
            }

            return response()->json([
                'status' => 'queued',
                'tickets_queued' => $tickets->count(),
                'message' => "Queued {$tickets->count()} tickets for classification"
            ]);
        } catch (\Exception $e) {
            \Log::error('Bulk classification failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to queue bulk classification jobs'
            ], 500);
        }
    }

    public function queueStatus()
    {
        try {
            $pendingJobs = \DB::table('jobs')->count();
            $failedJobs = \DB::table('failed_jobs')->count();
            
            return response()->json([
                'pending_jobs' => $pendingJobs,
                'failed_jobs' => $failedJobs,
                'queue_working' => $pendingJobs > 0 || $failedJobs > 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to check queue status',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function exportCsv()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="tickets.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID',
                'Subject',
                'Body',
                'Status',
                'Category',
                'Note',
                'Explanation',
                'Confidence',
                'Created At',
                'Updated At',
            ]);

            Ticket::chunk(200, function ($tickets) use ($handle) {
                foreach ($tickets as $ticket) {
                    fputcsv($handle, [
                        $ticket->id,
                        $ticket->subject,
                        $ticket->body,
                        $ticket->status,
                        $ticket->category ?? 'UNCLASSIFIED',
                        $ticket->note,
                        $ticket->explanation,
                        $ticket->confidence,
                        $ticket->created_at,
                        $ticket->updated_at,
                    ]);
                }
            });

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
