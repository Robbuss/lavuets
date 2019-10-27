<?php

use App\Models\Unit;
use App\Models\Customer;
use App\Models\Location;
use App\Scopes\CustomerScope;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMigrationLocationId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $customers = DB::table('customers')->get();
        foreach ($customers as $customer) {
            $location =  DB::table('locations')->insertGetId([
                'customer_id' => $customer->id,
                'name' => 'breukelen',
                'facility_name' => 'Breukelen',
            ]);

            $units = DB::table('units')->whereCustomerId($customer->id)->get();
            foreach ($units as $unit) {
                DB::table('units')
                    ->where('id', $unit->id)
                    ->update(['location_id' => $location]);
            }
        }

        Schema::table('units', function(Blueprint $table){
            $table->unsignedInteger('location_id')->nullable(false)->change();
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
