<?php

namespace App\Http\Controllers\Admin;

use App\Facades\TransactionService;
use App\Notifications\ReplenishmentOfBalanceByAdmin;
use App\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $transactions = TransactionService::applyFilters($request, Transaction::with('user'));

        $data = [ 
            'title' => 'Транзакции',
            'transactions' => $transactions->orderBy('created_at', 'desc')->paginate(25)->appends(Input::except('page'))
        ];

        return view('admin.transactions.index', array_merge($data, TransactionService::getFiltersVariables($request)));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'title' => 'Новая транзакция'
        ];

        return view('admin.transactions.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateForm($request);
        $transaction = Transaction::create($this->getRequest($request));
        if($transaction->type == 'income') {
            $transaction->user->notify(new ReplenishmentOfBalanceByAdmin($transaction));
        }

        return redirect()->route('admin.transactions.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        $data = [
            'title' => 'Редактирование транзакции',
            'transaction' => $transaction
        ];

        return view('admin.transactions.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        $this->validateForm($request);
        $transaction->update($this->getRequest($request));

        return redirect()->route('admin.transactions.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Transaction $transaction)
    {
        $json['success'] = false;
        try {
            $transaction->delete();
            $json['success'] = true;
        } catch (\Exception $e) {
            \Log::warning('Can\'t remove transaction', ['message' => $e->getMessage(), 'transaction' => $transaction]);
        }

        return Response::json($json);
    }

    private function getRequest(Request $request): array
    {
        $data = $request->all();
        if($data['type'] == 'expense') {
            if($data['amount'] > 0) {
                $data['amount'] = -$data['amount'];
            }

            if($data['amount_fact'] > 0) {
                $data['amount_fact'] = -$data['amount_fact'];
            }
        }

        return $data;
    }

    private function validateForm(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'comment' => 'nullable|max:255',
            'amount' => 'required|numeric',
            'amount_fact' => 'required|numeric',
            'type' => 'required'
        ]);
    }
}
