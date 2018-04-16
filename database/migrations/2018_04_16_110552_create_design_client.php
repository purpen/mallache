<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDesignClient extends Migration
{
    /**
     * company_name    varchar(100)    否        公司名称
     * contact_name    varchar(20)    否        联系人姓名
     * position    varchar(50)    否        职位
     * phone    varcahr(20)    否        手机
     * province    int(10)    否        省份
     * city    int(10)    否        城市
     * area    int(10)    否        地区
     * address    varchar(200)    否        详细地址
     * design_company_id    int(10)    否        设计公司ID
     */
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('design_client', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_name', 100);
            $table->string('contact_name', 20);
            $table->string('position')->default('');
            $table->string('phone', 20);
            $table->integer('province')->default(0);
            $table->integer('city')->default(0);
            $table->integer('area')->default(0);
            $table->string('address', 200);
            $table->integer('design_company_id');
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
        Schema::dropIfExists('design_client');
    }
}
