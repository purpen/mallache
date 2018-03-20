<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecycleBin extends Migration
{
    /**
     * pan_director_id    int(10)    否        文件ID
     * type    tinyint(4)    否        类型：1.文件夹、2.文件
     * name    varchar(100)    否        文件、目录名称
     * size    int(10)    否        文件大小
     * mime_type    varchar(50)    否        资源类型
     * user_id    int(10)    否        操作人ID
     * company_id    int(10)    否        设计公司ID
     */
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recycle_bin', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pan_director_id');
            $table->tinyInteger('type');
            $table->string('name');
            $table->integer('size');
            $table->string('mime_type', 50);
            $table->integer('user_id');
            $table->integer('company_id');
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
        Schema::dropIfExists('recycle_bin');
    }
}
