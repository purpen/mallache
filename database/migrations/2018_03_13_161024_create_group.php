<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroup extends Migration
{
    /**
    id	int(10)	否
    name	varchar(50)	否		群组名称
    user_id_arr	json	是	null	用户ID数组
    type	tinyint(4)	否		群组类型：1.系统创建 2. 用户创建
    company_id	int(10)	否		公司ID
    status	tinyint(4)	否	0	状态：
     */
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->json('user_id_arr')->nullable();
            $table->tinyInteger('type');
            $table->integer('company_id');
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('group');
    }
}
