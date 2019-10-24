<?php

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CoupleInvoicesToPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $payments = Payment::whereNull('invoice_id')->get();
        foreach($payments as $payment){
            $lastInvoice = Invoice::where('customer_id', $payment->customer_id)->orderBy('id', 'desc')->first();
            $payment->update(['invoice_id' => $lastInvoice->id]);
        }
        Schema::table('payments', function(Blueprint $table){
            $table->unsignedInteger('invoice_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            //
        });
    }
}
