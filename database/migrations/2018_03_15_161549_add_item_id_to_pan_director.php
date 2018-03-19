<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddItemIdToPanDirector extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pan_director', function (Blueprint $table) {
            $table->dropColumn(['group_id']);
        });

        Schema::table('pan_director', function (Blueprint $table) {
            $table->integer('item_id')->nullable();
            $table->json('group_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pan_director', function (Blueprint $table) {
            $table->dropColumn(['item_id']);
        });
    }
}
