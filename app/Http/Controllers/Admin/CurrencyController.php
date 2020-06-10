<?php

namespace App\Http\Controllers\Admin;

use Grechanyuk\CentralBankCurrency\Models\CentralBankCurrency;
use Grechanyuk\CentralBankCurrency\Facades\CentralBankCurrency as CentralBankCurrencyFacade;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Response;

class CurrencyController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Валюты',
            'currencies' => CentralBankCurrencyFacade::getCurrenciesList()
        ];

        return view('admin.currencies.index', $data);
    }

    public function update(Request $request)
    {
        $this->validateForm($request);

        foreach ($request->input('currencies') as $currency) {
            CentralBankCurrency::updateOrCreate([
                'id' => $currency['id']
            ], $currency);
        }

        return redirect()->route('admin.currencies.index')->with(['status' => 'success', 'message' => 'Успешно сохранено!']);
    }

    public function syncCurrencies()
    {
        $json['success'] = false;
        Artisan::call('central-bank:sync-currencies');
        $json['success'] = true;

        return Response::json($json);
    }

    private function validateForm(Request $request)
    {
        $v = \Validator::make($request->all(), [
            'currencies.*.name' => 'required|max:255|string',
            'currencies.*.par' => 'required|integer',
            'currencies.*.value' => 'required|numeric|max:255',
            'currencies.*.icon' => 'nullable|max:255|string'
        ]);

        $v->validate();
    }
}
