<?php


namespace App\Observers;


use Grechanyuk\CentralBankCurrency\Models\CentralBankCurrency;
use \Grechanyuk\CentralBankCurrency\Facades\CentralBankCurrency as CentralBankCurrencyFacade;

class CentralBankCurrencyObserver
{
    public function created(CentralBankCurrency $bankCurrency)
    {
        CentralBankCurrencyFacade::clearCache();
    }

    public function updated(CentralBankCurrency $bankCurrency)
    {
        CentralBankCurrencyFacade::clearCache();
    }

    public function deleted(CentralBankCurrency $bankCurrency)
    {
        CentralBankCurrencyFacade::clearCache();
    }
}