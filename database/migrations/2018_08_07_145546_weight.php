<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Weight extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weight', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('score')->default(0); //评分
            $table->tinyInteger('case')->default(0); //案列数量
            $table->tinyInteger('last_time')->default(0); //最近推荐时间
            $table->tinyInteger('success_rate')->default(0); //成功率
            $table->tinyInteger('average_price')->default(0); //接单均价
            $table->tinyInteger('area')->default(0); //地区
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
