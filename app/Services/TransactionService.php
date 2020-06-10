<?php
namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TransactionService
{
    public function applyFilters(Request $request, Builder $transactions): Builder
    {
        if($request->input('username')) {
            $transactions->whereHas('user', function ($query) use($request) {
                return $query->where('name', 'LIKE', '%' . $request->input('username') . '%');
            });
        }

        if($request->input('type')) {
            $transactions->where('type', $request->input('type'));
        }

        if($request->input('amount_fact')) {
            $transactions->where('amount_fact', $request->input('amount_fact'));
        }

        return $transactions;
    }

    public function getFiltersVariables(Request $request)
    {
        return [
            'filterUsername' => $request->input('username'),
            'filterType' => $request->input('type'),
            'filterAmountFact' => $request->input('amount_fact')
        ];
    }
}