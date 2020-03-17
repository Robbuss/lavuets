<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('key');
            $table->mediumText('value');
            $table->timestamps();
        });

        $settings = [
            'app_name' => 'App name',
            'enable_registration' => '0',
            'primary_color' => '#6786A1',
            'secondary_color' => '#6786A1',
            'mollie_api_key' => 'mollie api key',
            'mollie_webhook_url' => 'mollie webhook',
        ];
        foreach ($settings as $key => $value) {
            Setting::create([
                'key' => $key,
                'value' => $value
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
        Schema::dropIfExists('settings');
    }
}
