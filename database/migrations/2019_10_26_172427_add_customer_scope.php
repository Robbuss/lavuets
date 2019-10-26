<?php

use App\Models\Customer;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomerScope extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function(Blueprint $table){
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Customer::create([
            'domain' => 'boekonline',
            'name' => 'Opslagmagazijn',
            'company_name' => 'Opslagmagazijn B.V.',
            'email' => 'nigel@opslagmagazijn.nl'
        ]);
        
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedInteger('customer_id')->after('id')->default(1);
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('tenant_id')->references('id')->on('tenants');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedInteger('customer_id')->after('id')->default(1);
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('tenant_id')->references('id')->on('tenants');
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->unsignedInteger('customer_id')->after('id')->default(1);
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('tenant_id')->references('id')->on('tenants');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('customer_id')->after('id')->default(1);
            $table->foreign('customer_id')->references('id')->on('customers');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->unsignedInteger('customer_id')->after('id')->default(1);
            $table->foreign('customer_id')->references('id')->on('customers');
        });

        Schema::table('units', function (Blueprint $table) {
            $table->unsignedInteger('customer_id')->after('id')->default(1);
            $table->foreign('customer_id')->references('id')->on('customers');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->unsignedInteger('customer_id')->after('id')->default(1);
            $table->foreign('customer_id')->references('id')->on('customers');
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
