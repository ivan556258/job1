<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static applyFilters(\Illuminate\Http\Request $request, \Illuminate\Database\Eloquent\Builder $transactions)
 * @method static getFiltersVariables(\Illuminate\Http\Request $request)
 */
class TransactionService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'transaction_service';
    }
}