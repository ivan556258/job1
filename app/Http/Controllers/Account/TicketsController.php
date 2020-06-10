<?php

namespace App\Http\Controllers\Account;

use App\Status;
use App\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketsController extends TicketMessageController
{
    protected function redirectTo()
    {
        return redirect()->route('account.tickets.index');
    }

    public function index()
    {
        $data = [
            'meta_title' => 'Тикеты',
            'tickets' => Auth::user()->tickets()->with(['status', 'lastMessage', 'messages'])->paginate(25),
            'statuses' => Status::all()
        ];

        return view('front.account.tickets.index', $data);
    }

    public function create(Request $request)
    {
        $data = [
            'meta_title' => 'Новый тикет',
            'activeSubject' => $request->input('activeSubject'),
            'ticketTitle' => $request->input('ticketTitle')
        ];

        return view('front.account.tickets.create', $data);
    }

    public function store(Request $request)
    {
        $this->validateForm($request);
        $ticket = Auth::user()->tickets()->create($request->all());

        return $this->storeMessage($ticket, $request);
    }

    public function show(Ticket $ticket)
    {
        $data = [
            'meta_title' => 'Тикет №'.$ticket->id,
            'ticket' => $ticket
        ];

        return view('front.account.tickets.show', $data);
    }

    private function validateForm(Request $request)
    {
        $this->validate($request, [
            'subject' => 'required|max:255',
            'title' => 'required|max:255',
            'message' => 'required'
        ]);
    }
}
