<?php

use App\Models\Invoice;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRefNumberToInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            //
            $table->string('ref_number')->after('ref')->nullable();
        });

        $invoices = Invoice::all();
        foreach($invoices as $invoice){
            $invoice->update(['ref_number' => $invoice->id]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
