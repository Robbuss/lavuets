<?php

use App\Models\Invoice;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableInvoiceUnit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_unit', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('invoice_id');
            $table->unsignedInteger('unit_id');
            $table->decimal('price', 8, 2);
            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->foreign('unit_id')->references('id')->on('units');
            $table->timestamps();
        });

        $invoices = Invoice::all();
        foreach ($invoices as $invoice) {
            $invoice->units()->sync($this->getSyncArray($invoice->contract->units));
        }
    }

    public function getSyncArray($priceArray)
    {
        $invoiceUnitPrice = [];
        foreach ($priceArray as $pu) {
            $invoiceUnitPrice[$pu['id']] = ['price' => $pu['price']];
        };
        return $invoiceUnitPrice;
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    { }
}
