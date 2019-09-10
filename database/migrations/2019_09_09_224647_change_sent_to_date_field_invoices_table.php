<?php

use Carbon\Carbon;
use App\Models\Invoice;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeSentToDateFieldInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign('invoices_payment_id_foreign');
            $table->date('sent')->after('note')->nullable();
        });
        DB::statement("ALTER TABLE invoices MODIFY COLUMN payment_id INT AFTER customer_id");
        $invoices = Invoice::all();
        foreach ($invoices as $invoice) {
            $invoice->update([
                'sent' => Carbon::now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
