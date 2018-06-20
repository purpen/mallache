<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPayStatusToItemStage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_stage', function (Blueprint $table) {
            $table->tinyInteger('pay_status')->default(0);
        });

        Schema::table('pay_order', function (Blueprint $table) {
            $table->integer('item_stage_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_stage', function (Blueprint $table) {
            $table->dropColumn(['pay_status']);
        });

        Schema::table('pay_order', function (Blueprint $table) {
            $table->dropColumn(['item_stage_id']);
        });
    }
}
