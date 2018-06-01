<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTrueTimeToContract extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract', function (Blueprint $table) {
            $table->integer('demand_pay_limit')->nullable();
            $table->integer('thn_pay_limit')->nullable();
            $table->integer('true_time')->nullable();

        });

        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('source')->default(0);
        });

        Schema::table('item', function (Blueprint $table) {
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
        Schema::table('contract', function (Blueprint $table) {
            $table->dropColumn(['demand_pay_limit', 'thn_pay_limit', 'true_time']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['source']);
        });

        Schema::table('item', function (Blueprint $table) {
            $table->dropColumn(['source']);
        });
    }
}
