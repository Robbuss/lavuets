<?php

namespace App\Console\Commands;

use App\Models\Contract;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FreeUnitsExpiredContract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contract:expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Free the unitsi of Contracts that are expired.';

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
        $contracts = Contract::whereNotNull('deactivated_at')->get();
        $count = 0;
        foreach($contracts as $contract){
            if(Carbon::now() > $contract->invoices()->orderBy('end_date', 'desc')->first()->end_date){
                $count += $contract->units()->count();
                $contract->units()->detach();
            }
        }
        activity('crontab')->log('Freeing ' . $count . ' units');
    }
}
