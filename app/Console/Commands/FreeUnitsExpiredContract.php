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
        activity()->log('Running Crontab. Freeing ' . count($contracts). ' units');
        foreach($contracts as $contract){
            if(Carbon::now() > Carbon::parse($contract->deactivated_at)->{$contract->method}($contract->period)){
                $contract->units()->detach();
            }
        }
    }
}
