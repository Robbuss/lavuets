<?php

use Spatie\MediaLibrary\Models\Media;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeMediaFilesToPrivate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $media = Media::all();
        foreach($media as $m){
            $m->disk = 'private';
            $m->save();
        }
        if(file_exists(storage_path(('app\public'))))
            rename(storage_path('app\public'), storage_path('app\private'));
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
