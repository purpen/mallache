<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticle extends Migration
{


//    字段	类型	空	默认值	注释
//    id	int(10)	否
//    title	varchar(50)	否		标题
//    content	varchar(1000)	no		内容
//    classification_id	int(11)	no		分类ID
//    status	tinyint(4)	no	0	状态：0.未发布;1.发布
//    recommend	timestamp	no	null	推荐时间
//    read_amount	int(11)	no	0	阅读数量
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 50);
            $table->string('content', 1000);
            $table->integer('classification_id');
            $table->tinyInteger('status')->default(0);
            $table->timestamp('recommend')->nullable();
            $table->integer('read_amount')->default(0);
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
        Schema::dropIfExists('article');
    }
}
