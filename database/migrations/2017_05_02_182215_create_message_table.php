<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageTable extends Migration
{
//    message 通知表
//    字段	类型	空	默认值	注释
//    id	int(10)	否
//    user_id	int(10)	否		用户ID
//    type	tinyint(4)	是	0	消息类型；1.系统通知。
//    content	varchar(100)	否		内容
//    status	tinyint(4)	否		状态
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->tinyInteger('type')->default(0);
            $table->string('content', 100)->defaualt('');
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
            $table->index(['user_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message');
    }
}
