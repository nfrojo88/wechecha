<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('user_id', auth()->id())
            ->withCount('replies')
            ->latest()
            ->paginate(15);

        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('tickets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category'    => 'required|in:system_bug,feature_request,access_issue,account_help,other',
            'subject'     => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'priority'    => 'required|in:low,medium,high,urgent',
        ]);

        $ticket = SupportTicket::create([
            'user_id'     => auth()->id(),
            'category'    => $request->category,
            'subject'     => $request->subject,
            'description' => $request->description,
            'priority'    => $request->priority,
            'status'      => 'open',
        ]);

        \App\Models\ActivityLog::log('created', 'Support ticket ' . $ticket->ticket_no . ' opened: ' . $request->subject, 'Support Tickets', $ticket);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Your ticket ' . $ticket->ticket_no . ' has been submitted. Our admin team will respond shortly.');
    }

    public function show(SupportTicket $ticket)
    {
        // Users can only view their own tickets
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        $ticket->load(['replies.user', 'assignedTo']);

        return view('tickets.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'message' => 'required|string|max:3000',
        ]);

        TicketReply::create([
            'ticket_id'     => $ticket->id,
            'user_id'       => auth()->id(),
            'message'       => $request->message,
            'is_admin_reply'=> false,
        ]);

        // Re-open if closed/resolved by admin
        if (in_array($ticket->status, ['resolved', 'closed'])) {
            $ticket->update(['status' => 'open']);
        }

        return back()->with('success', 'Your reply has been sent.');
    }
}
