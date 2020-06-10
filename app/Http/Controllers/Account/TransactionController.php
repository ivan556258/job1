<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Transaction;
use Grechanyuk\FreeKassa\Facades\FreeKassa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
 
class TransactionController extends Controller
{
    public function index()
    {
        $data = [ 
            'meta_title' => 'Лицевой счёт',
            'incomes' => Auth::user()->transactions()->incomeTransactions()->where('created_at', '>=', Carbon::today()->subDays(45))->get(),
            'expenses' => Auth::user()->transactions()->expenseTransactions()->where('created_at', '>=', Carbon::today()->subDays(45))->get(),
            'currencies' => FreeKassa::getCurrencyList()
        ]; 

        return view('front.account.transactions.index', $data);
    }
 
    public function create(Request $request)
    {
        $json['success'] = false;
        $v = $this->validateForm($request);

        if($v->fails()) {
            $json['errors'] = $v->errors();
            return Response::json($json);
        }

        $transaction = Transaction::create([
            'payment_method' => $request->input('payment_method') ? FreeKassa::getCurrencyISO($request->input('payment_method')) : null,
            'status' => 0,
            'amount' => $request->input('amount'),
            'amount_fact' => $request->input('amount'),
            'comment' => 'Пополнение баланса',
            'user_id' => Auth::id(),
            'type' => 'income'
        ]);

        $json['redirect'] = FreeKassa::newPayment($transaction, $request->input('payment_method'));
        $json['success'] = true;

        return Response::json($json);
    }

    public function success()
    {
        return redirect()->route('account.transactions.index')
            ->with(['status' => 'success', 'message' => 'Оплата совершена успешно! Она будет зачислена после того, как платежный агрегатор подтвердит платеж']);
    }

    public function fail()
    {
        return redirect()->route('account.transactions.index')
            ->with(['status' => 'danger', 'message' => 'Оплата не удалась!']);
    }

    private function validateForm(Request $request)
    {
        $v = \Validator::make($request->all(), [
            'amount' => 'required|numeric'
        ]);

        return $v;
    }
}
