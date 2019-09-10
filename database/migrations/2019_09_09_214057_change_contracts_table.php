<?php

use App\Models\Contract;
use App\Utils\InvoiceGenerator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts', function(Blueprint $table){
            $table->dropColumn('bail_paid');
            $table->dropColumn('bail_sum');
            $table->dropColumn('bail');
            $table->integer('auto_invoice')->after('payment_method')->default(0);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('sent');
        });

        $contracts = Contract::doesntHave('invoices')->get();
        foreach($contracts as $contract){
            new InvoiceGenerator($contract);
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
