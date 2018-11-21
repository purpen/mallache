<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPayOrderDelete extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pay_order', function (Blueprint $table) {
            $table->tinyInteger('design_delete')->default(1);
            $table->tinyInteger('demand_delete')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pay_order', function (Blueprint $table) {
            $table->dropColumn(['demand_delete','design_delete']);
        });
    }
}
