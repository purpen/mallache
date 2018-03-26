<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePanDirectorIdArrToPanShare extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pan_share', function (Blueprint $table) {
            $table->dropColumn(['pan_director_id']);
            $table->json('pan_director_id_arr')->nullable();
            $table->integer('share_time')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pan_share', function (Blueprint $table) {
            //
        });
    }
}
