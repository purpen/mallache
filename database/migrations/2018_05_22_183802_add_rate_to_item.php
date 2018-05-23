<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRateToItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item', function (Blueprint $table) {
            $table->tinyInteger('commission_rate')->default(0);
            $table->tinyInteger('preferential_type')->default(0);
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
            $table->dropColumn(['commission_rate', 'preferential_type']);
        });
    }
}
