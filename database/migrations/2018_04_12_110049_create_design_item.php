<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDesignItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    /**
     * name    varchar(100)    否        项目名称
     * description    text    是    ‘’    项目描述
     * level    tinyint(4)        null    项目等级 1.普通 2.紧急 3.非常紧急
     * business_manager    int(10)        0    商务经理
     * leader    int(10)        0    项目负责人
     * cost    decimal(10,2)        null    项目费用
     * workplace    varcahr(50)        ''    工作地点
     * type_value    varchar(100)        ''    设计类别
     * field    tinyint(4)    是    null    产品所属领域
     * industry    tinyint(4)    是    null    产品所属行业
     * start_time    int(10)        null    项目开始时间
     * project_duration    int(10)        null    项目工期
     * company_name    varchar(100)        ''    客户名称
     * contact_name    varchar(50)    是    ‘’    联系人姓名
     * position    varchar(50)        ''    职位
     * phone    varcahr(20)        ''    手机
     * province    int(10)        null    省份
     * city    int(10)        null    城市
     * area    int(10)        null    地区
     * address    varchar(100)        ''    详细地址
     * type    tinyint(4)    否        1.线上项目生成 2.线下项目
     * user_id    int(10)    否        创建人
     */
    public function up()
    {
        Schema::create('design_project', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('design_company_id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->tinyInteger('level')->nullable();
            $table->integer('business_manager')->default(0);
            $table->integer('leader')->default(0);
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('workplace', 50)->default('');
            $table->string('type_value', 100)->default('');
            $table->tinyInteger('field')->nullable();
            $table->tinyInteger('industry')->nullable();
            $table->integer('start_time')->nullable();
            $table->integer('project_duration')->nullable();
            $table->string('company_name', 100)->default('');
            $table->string('contact_name', 50)->default('');
            $table->string('position', 50)->default('');
            $table->string('phone', 20)->default('');
            $table->integer('province')->nullable();
            $table->integer('city')->nullable();
            $table->integer('area')->nullable();
            $table->string('address', 20)->default('');
            $table->tinyInteger('type');
            $table->integer('user_id');

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
        Schema::dropIfExists('design_project');
    }
}
