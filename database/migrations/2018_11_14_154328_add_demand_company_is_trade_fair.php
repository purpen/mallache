<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDemandCompanyIsTradeFair extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('demand_company', function (Blueprint $table) {
            $table->tinyInteger('is_trade_fair')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('demand_company', function (Blueprint $table) {
            $table->dropColumn(['is_trade_fair']);
        });
    }
}
