<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DefaultFieldsCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function(Blueprint $table){
            $table->string('name')->nullable(false)->change();
            $table->string('city')->nullable()->change();
            $table->string('street_addr')->nullable()->change();
            $table->string('street_number')->nullable()->change();
            $table->string('postal_code')->nullable()->change();
        });

        Schema::table('invoices', function(Blueprint $table){
            $table->unsignedInteger('customer_id')->default(NULL)->change();
        });

        Schema::table('contracts', function(Blueprint $table){
            $table->unsignedInteger('customer_id')->default(NULL)->change();
        });

        Schema::table('locations', function(Blueprint $table){
            $table->unsignedInteger('customer_id')->default(NULL)->change();
        });

        Schema::table('payments', function(Blueprint $table){
            $table->unsignedInteger('customer_id')->default(NULL)->change();
        });

        Schema::table('settings', function(Blueprint $table){
            $table->unsignedInteger('customer_id')->default(NULL)->change();
        });

        Schema::table('tenants', function(Blueprint $table){
            $table->unsignedInteger('customer_id')->default(NULL)->change();
        });

        Schema::table('units', function(Blueprint $table){
            $table->unsignedInteger('customer_id')->default(NULL)->change();
        });

        Schema::table('users', function(Blueprint $table){
            $table->unsignedInteger('customer_id')->default(NULL)->change();
        });        
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
