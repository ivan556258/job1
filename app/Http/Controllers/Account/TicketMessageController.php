<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketMessageController extends Controller
{
    public function storeMessage(Ticket $ticket, Request $request)
    {
        $this->validateForm($request);
        $data = $request->all();
        $data['user_id'] = Auth::id();
        $message = $ticket->messages()->create($data);

        if($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $message->images()->create([
                    'image' => $image->store('tickets/'.$ticket->id, 'public')
                ]);
            }
        }

        return $this->redirectTo();
    }

    protected function redirectTo() {
        return redirect()->back();
    }

    private function validateForm(Request $request)
    {
        $this->validate($request, [
            'message' => 'required'
        ]);
    }
}
