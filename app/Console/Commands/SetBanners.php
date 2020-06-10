<?php

namespace App\Console\Commands;

use App\Facades\SalonAdvertising;
use Illuminate\Console\Command;

class SetBanners extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'banners:set {city_id?} {--pay}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up banners';

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
        $pay = false;
        $city_id = null;

        if($this->option('pay')) {
            $pay = true;
        }

        if($this->hasArgument('city_id')) {
            $city_id = $this->argument('city_id');
        }

        SalonAdvertising::setBanners($city_id, $pay);
        $this->info('All done!');
    }
}
