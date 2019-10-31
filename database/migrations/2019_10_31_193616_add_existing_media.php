<?php

use App\Models\Invoice;
use App\Utils\PdfGenerator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExistingMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // get invoices without media
        $invoices = Invoice::doesntHave('media')->get();
        foreach($invoices as $invoice){
            (new PdfGenerator($invoice))->generateInvoice();
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
