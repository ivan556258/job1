<?php

namespace App\Console\Commands;

use App\Facades\ProfileTransactions;
use App\Facades\SalonService;
use Illuminate\Console\Command;

class PayMounthlyPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pay:everymonth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command will be create transactions for Profiles & Salons every month payments';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ProfileTransactions::updateVerification();
        SalonService::everyMonthActivation();
    }
}
