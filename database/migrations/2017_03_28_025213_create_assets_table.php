<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetsTable extends Migration
{
    /*
     *
        id	        int(11)	    否		ID
        user_id	    int(11)	    是		用户ID
        target_id	int(11)	    是		关联ID
        type	    tinyint(1)	是	1	附件类型: 1.默认；
        name	    varchar(50)	否		文件名
        summary	    varchar(100)是		文件描述
        random	    varchar(30)	是		随机字符串(回调查询)
        path	    varchar(100)否		文件路径
        size	    int(11)	    否	0	文件大小
        width	    int(11)	    是	0	宽
        height	    int(11)	    是	0	高
        mime	    varchar(10)	否		文件类型
        domain	    varchar(10)	否		存储域
        status	    tinyint(1)	否	1	状态：0.否；1.是
     */

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('target_id')->nullable();
            $table->tinyInteger('type')->default(1);
            $table->string('name',50);
            $table->string('summary',100)->nullable();
            $table->string('path',100);
            $table->integer('size')->default(0);
            $table->integer('width')->default(0);
            $table->integer('height')->default(0);
            $table->string('mime',10);
            $table->string('domain',10);
            $table->tinyInteger('status')->default(1);
            $table->string('random',30);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assets');
    }
}
