<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::where('receiver_id', auth()->id())
            ->with(['sender'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('communication.messages.index', compact('messages'));
    }

    public function create()
    {
        $users = User::where('id', '!=', auth()->id())->where('is_active', true)->get();
        return view('communication.messages.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string',
            'parent_id' => 'nullable|exists:messages,id'
        ]);
        $data['sender_id'] = auth()->id();
        $message = Message::create($data);
        return redirect()->route('messages.index')->with('success', 'Message sent successfully.');
    }

    public function show(Message $message)
    {
        if ($message->receiver_id !== auth()->id() && $message->sender_id !== auth()->id()) {
            abort(403);
        }

        if ($message->receiver_id === auth()->id() && !$message->read_at) {
            $message->update(['read_at' => now(), 'status' => 'read']);
        }

        $message->load(['sender', 'receiver', 'replies.sender']);
        return view('communication.messages.show', compact('message'));
    }

    public function reply(Request $request, Message $message)
    {
        $request->validate(['body' => 'required|string']);
        
        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $message->sender_id === auth()->id() ? $message->receiver_id : $message->sender_id,
            'subject' => 'Re: ' . $message->subject,
            'body' => $request->body,
            'parent_id' => $message->id,
        ]);

        return redirect()->route('messages.show', $message)->with('success', 'Reply sent successfully.');
    }
}
