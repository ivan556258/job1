<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Account\TicketMessageController;
use App\Status;
use App\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class TicketController extends TicketMessageController
{
    public function index()
    {
        $data = [
            'title' => 'Тикеты',
            'tickets' => Ticket::with(['lastMessage', 'status'])->paginate(25)
        ];

        return view('admin.tickets.index', $data);
    }

    public function show(Ticket $ticket)
    {
        $data = [
            'title' => 'Просмотр тикета',
            'ticket' => $ticket->load(['messages', 'messages.images']),
            'statuses' => Status::all()
        ];

        return view('admin.tickets.show', $data);
    }

    public function changeStatus(Request $request, Ticket $ticket)
    {
        $json['success'] = false;
        if ($request->has('status_id')) {
            $ticket->update($request->all());
            $json['success'] = true;
        }

        return Response::json($json);
    }

    public function destroy(Ticket $ticket)
    {
        $json['success'] = false;
        try {
            $ticket->delete();
            $json['success'] = true;
        } catch (\Exception $e) {
            \Log::warning('Cant remove ticket', ['message' => $e->getMessage(), 'ticket' => $ticket]);
        }

        return Response::json($json);
    }
}
