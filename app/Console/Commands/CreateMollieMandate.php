<?php

namespace App\Console\Commands;

use App\Models\Customer;
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
    protected $description = 'Create Mollie mandates for customers with iban';

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
        $customers = Customer::whereHas('contracts', function ($q) {
            $q->where('payment_method', 'incasso');
        })->whereNotNull('iban')->whereNull('mandate_id')->get();
        $count = 0;
        foreach ($customers as $customer) {
            // make sure there are no spaces in iban; else mollie will rage. Fix this in frontend
            $customer->update(['iban' => str_replace(' ', '', $customer->iban)]);
            if (!$customer->mollie_id) {
                //create mollie customer 
                $mollieCustomer = Mollie::api()->customers()->create([
                    "name"  => $customer->name,
                    "email"  => $customer->email,
                ]);

                // save mollie id
                $customer->update(['mollie_id' => $mollieCustomer->id]);
            }

            $mollieCustomer = Mollie::api()->customers()->get($customer->mollie_id);
            $mandates = Mollie::api()->mandates()->listFor($mollieCustomer);

            if ($mandates->count === 0) {
                // create a mandate  
                try{
                    $mandate = Mollie::api()->customers()->get($customer->mollie_id)->createMandate([
                        "method" => \Mollie\Api\Types\MandateMethod::DIRECTDEBIT,
                        "consumerName" => $customer->name,
                        "consumerAccount" => $customer->iban,
                        "signatureDate" => \Carbon\Carbon::now()->format('Y-m-d'),
                        "mandateReference" => "OPSLAGMAG-" . $customer->id,
                    ]);
                }catch(\Exception $e){
                    activity('crontab')->log('Error mandaat maken voor: '. $customer->name . ' iban: ' . $customer->iban);
                }

                $customer->update(['mandate_id' => $mandate->id]);
            }
            $count++;
        }
        activity('crontab')->log('Created ' . $count . ' mandates');
    }
}
