<?php

namespace App\Console\Commands;

use App\Notifications\MonthlyPaymentForProfile;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendScheduleEmailNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:send-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notifications to users with important information about coming payments';

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
        $days = 7;
        $users = User::whereHas('profiles', function ($query) use ($days) {
            $query->payVerification(-30 + $days)
                ->where('verification_at', '>', Carbon::now()->addDays(- 31 + $days));
        })->get();

        foreach ($users as $user) {
            $user->notify(new MonthlyPaymentForProfile($user, $days));
        }
    }
}
