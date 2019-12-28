<?php

use App\Models\Customer;
use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class GenerateEndPoints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $customers = Customer::all();
        foreach ($customers as $customer) {
            Setting::create([
                'public' => '0',
                'key' => 'login_endpoint',
                'value' => 'https://' . $customer->domain . '.' . config('app.domain') . '/oauth/token',
                'customer_id' => $customer->id,
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
