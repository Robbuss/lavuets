<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('customers', 'tenants');
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('domain');
            $table->string('name')->nullable();
            $table->string('company_name');
            $table->string('email');
            $table->string('city')->nullable();
            $table->string('street_addr')->nullable();
            $table->string('street_number')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('btw')->nullable();
            $table->string('kvk')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('invoices', function(Blueprint $table){
            $table->dropForeign(['customer_id']);
            $table->dropIndex('invoices_customer_id_foreign');
            $table->renameColumn('customer_id', 'tenant_id');
        });

        Schema::table('payments', function(Blueprint $table){
            $table->dropForeign(['customer_id']);
            $table->dropIndex('payments_customer_id_foreign');
            $table->renameColumn('customer_id', 'tenant_id');
        });

        Schema::table('contracts', function(Blueprint $table){
            $table->dropForeign(['customer_id']);
            $table->dropIndex('contracts_customer_id_foreign');
            $table->renameColumn('customer_id', 'tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    { }
}
