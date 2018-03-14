<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemLevelsTable extends Migration
{
    /**
    id	int(10)	否
    name	varchar(20)	是		名称
    summary	varchar(500)	否		描述
    user_id	int(5)	是		创建人ID
    type	tinyint(1)	是	1	类型;1.默认；
    status	tinyint(1)	是	1	状态：0.禁用；1.启用；
     */
    public function up()
    {
        Schema::create('item_levels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 20)->nullable();
            $table->string('summary', 500)->nullable();
            $table->integer('user_id');
            $table->tinyInteger('status');
            $table->tinyInteger('type');
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
        Schema::dropIfExists('item_levels');
    }
}
