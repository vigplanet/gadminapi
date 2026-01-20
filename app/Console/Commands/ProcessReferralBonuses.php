<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ProcessReferralBonusAfterReturnPeriod;
use App\Models\Order;
use Carbon\Carbon;

class ProcessReferralBonuses extends Command
{
    protected $signature = 'referral:process-bonuses';

    protected $description = 'Dispatch referral bonus processing jobs for eligible orders';

    public function handle()
    {
       \Artisan::call('queue:listen');
    }
}