<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassification extends Migration
{

//    字段	类型	空	默认值	注释
//    id	int(11)
//    type	tinyint(4)	no		大分类：1.文章；
//    name	varchar(50)	no		栏目名称
//    content	varchar(500)	是	“”	栏目描述

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classification', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type');
            $table->string('name', 50);
            $table->string('content', 500)->default('');
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
        Schema::dropIfExists('classification');
    }
}
