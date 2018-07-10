<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSourceToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('design_company', function (Blueprint $table) {
            $table->tinyInteger('source')->default(0);
        });

        Schema::table('demand_company', function (Blueprint $table) {
            $table->tinyInteger('source')->default(0);
        });

        Schema::table('pay_order', function (Blueprint $table) {
            $table->tinyInteger('source')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('design_company', function (Blueprint $table) {
            $table->dropColumn(['source']);
        });

        Schema::table('demand_company', function (Blueprint $table) {
            $table->dropColumn(['source']);
        });

        Schema::table('pay_order', function (Blueprint $table) {
            $table->dropColumn(['source']);
        });
    }
}
