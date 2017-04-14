<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColumnTable extends Migration
{
    /*
        id	    int(10)	        否
        type	tinyint(4)	    否		栏目类型
        name	varchar(50)	    否		栏目名称
        content	varchar(200)	否		内容
        url	    varchar(200)	否		链接
        sort	int(10)	        否	0	排序
        status	tinyint(4)	    否		状态
     */
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('column', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type');
            $table->string('name' , 50);
            $table->string('content' , 200);
            $table->string('url' , 200);
            $table->integer('sort')->default(0);
            $table->tinyInteger('status');
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
        Schema::dropIfExists('column');
    }
}
