<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractUnitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_unit', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('contract_id');
            $table->unsignedInteger('unit_id');
            $table->integer('price');
            $table->foreign('contract_id')->references('id')->on('contracts');
            $table->foreign('unit_id')->references('id')->on('units');
            $table->timestamps();
        });

        Schema::table('contracts', function(Blueprint $table){
            $table->tinyInteger('active')->after('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('contract_unit');
    }
}
