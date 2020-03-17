<?php

use App\Models\Unit;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveM2FromDescriptionsToField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $units = Unit::all();
        foreach ($units as $unit) {
            $m2string = preg_match('/\((.*m2)\)/', $unit->name, $out) ? $out[0] : false;
            if ($m2string) {
                $m2 = preg_match('/(?<=\().*(?=m2\))/', $unit->name, $out) ? $out[0] : false;
                $unit->size_m2 = floatval($m2);
                $unit->name = preg_replace('/.\((.*m2)\)/', '', $unit->name);
                $unit->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
