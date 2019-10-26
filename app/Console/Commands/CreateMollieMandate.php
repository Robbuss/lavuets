<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Mollie\Laravel\Facades\Mollie;

class CreateMollieMandate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mollie:mandates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Mollie mandates for tenants with iban';

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
        $tenants = Tenant::whereHas('contracts', function ($q) {
            $q->where('payment_method', 'incasso');
        })->whereNotNull('iban')->whereNull('mandate_id')->get();
        $count = 0;
        foreach ($tenants as $tenant) {
            // make sure there are no spaces in iban; else mollie will rage. Fix this in frontend
            $tenant->update(['iban' => str_replace(' ', '', $tenant->iban)]);
            if (!$tenant->mollie_id) {
                //create mollie tenant 
                $mollieCustomer = Mollie::api()->customers()->create([
                    "name"  => $tenant->name,
                    "email"  => $tenant->email,
                ]);

                // save mollie id
                $tenant->update(['mollie_id' => $mollieCustomer->id]);
            }

            $mollieCustomer = Mollie::api()->customers()->get($tenant->mollie_id);
            $mandates = Mollie::api()->mandates()->listFor($mollieCustomer);

            if ($mandates->count === 0) {
                // create a mandate  
                try{
                    $mandate = Mollie::api()->customers()->get($tenant->mollie_id)->createMandate([
                        "method" => \Mollie\Api\Types\MandateMethod::DIRECTDEBIT,
                        "consumerName" => $tenant->name,
                        "consumerAccount" => $tenant->iban,
                        "signatureDate" => \Carbon\Carbon::now()->format('Y-m-d'),
                        "mandateReference" => "OPSLAGMAG-" . $tenant->id,
                    ]);
                }catch(\Exception $e){
                    activity('crontab')->log('Error mandaat maken voor: '. $tenant->name . ' iban: ' . $tenant->iban);
                }

                $tenant->update(['mandate_id' => $mandate->id]);
            }
            $count++;
        }
        activity('crontab')->log('Created ' . $count . ' mandates');
    }
}
