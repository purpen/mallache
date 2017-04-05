<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUDesignsTable extends Migration
{
    /*
    u_design UX/UI 设计项目表，
    字段	类型	空	默认值	注释
    id	int(10)	否		ID
    item_id	int(10)	否	0	项目ID
    system	tinyint(4)	否		系统：1.ios；2.安卓；
    design_content	tinyint(4)	否		设计内容：1.视觉设计；2.交互设计；
    page_number	tinyint(4)	否		页面数量：1.10页以内；2.10-30'；3.30-50;4.50-；
    name	varchar(50)	否		项目名称
    stage	tinyint(4)	是		阶段：1、已有app／网站，需重新设计；2、没有app／网站，需要全新设计；
    complete_content	tinyint(4)	是		已完成设计内容：1.流程图；2.线框图；3.页面内容；4.产品功能需求点；5.其他
    other_content	varchar(20)	是		其他设计内容
    style	tinyint(4)	是		ui设计风格：1.简约；2.扁平化；3.拟物；
    start_time	tinyint(4)	否		项目开始时间：1、可商议；2、马上开始；3、有具体时间；
    cycle	tinyint(4)	否		项目周期：1.1-2周；2.2-4周；3.1-2月；4.2月以上；5.不确定
    design_cost	tinyint(4)	否		设计费用：
    province	int(10)	否		省份
    city	int(10)	否		城市
    summary	varchar(500)	是		备注
    artificial	tinyint(4)	是	0	人工服务
    */
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('u_design', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id')->default(0);
            $table->tinyInteger('system');
            $table->tinyInteger('design_content');
            $table->tinyInteger('page_number');
            $table->string('name',50);
            $table->tinyInteger('stage')->default(0);
            $table->tinyInteger('complete_content')->default(0);
            $table->string('other_content',20)->default('');
            $table->tinyInteger('style')->default(0);
            $table->tinyInteger('start_time');
            $table->tinyInteger('cycle');
            $table->tinyInteger('design_cost');
            $table->integer('province');
            $table->integer('city');
            $table->string('summary', 500)->default('');
            $table->tinyInteger('artificial')->default(0);
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
        Schema::dropIfExists('u_design');
    }
}
