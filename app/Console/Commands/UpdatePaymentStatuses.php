<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;
use Mollie\Laravel\Facades\Mollie;

class UpdatePaymentStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the status for all unpaid payments';

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
        $unpaid = Payment::whereNotIn('status', ['paid', 'canceled', 'expired'])->orWhereNull('status')->whereNotNull('payment_id')->get();
        foreach ($unpaid as $payment) {
            try {
                $molliePayment =  Mollie::api()->payments()->get($payment->payment_id);
                $payment->update([
                    'status' => $molliePayment->status,
                    'mode' => $molliePayment->mode,
                    'amount' => $molliePayment->amount->value,
                ]);
                print_r('Updated' . $payment->id);
            } catch (\Exception $e) {
                activity('crontab')->log('Error payment update: ' . $payment->payment_id);
            }
        }
    }
}
