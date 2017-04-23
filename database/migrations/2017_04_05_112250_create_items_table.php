<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /*
    item 项目表
    字段	类型	空	默认值	注释
    id	int(10)	否
    design_type	tinyint(4)	否		设计类别：1.产品策略；2.产品设计；3.结构设计；4.app设计；5.网页设计；
    field	int(10)	否		所属领域： class_id
    status	tinyint(4)	是		状态：1.准备；2.发布；3失败；4成功；5.取消；
    */

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->tinyInteger('design_type')->default(0);
            $table->integer('field')->default(0);
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
        Schema::dropIfExists('item');
    }
}
