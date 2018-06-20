<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCycleToItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item', function (Blueprint $table) {
            $table->tinyInteger('cycle')->default(0);
            $table->tinyInteger('design_cost')->default(0);
            $table->tinyInteger('industry')->default(0);
            $table->integer('item_province')->default(0);
            $table->integer('item_city')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item', function (Blueprint $table) {
            $table->dropColumn(['cycle' , 'design_cost' , 'industry' , 'item_province' , 'item_city']);
        });
    }
}
