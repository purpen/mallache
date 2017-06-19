<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTrueTimeToWithdrawOrder extends Migration
{
    /*true_time	timestamp	是		确认时间
    true_user_id	int(10)	是		确认用户*/
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('withdraw_order', function (Blueprint $table) {
            $table->timestamp('true_time')->nullable();
            $table->integer('true_user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('withdraw_order', function (Blueprint $table) {
            //
        });
    }
}
