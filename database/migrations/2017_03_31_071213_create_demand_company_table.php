<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDemandCompanyTable extends Migration
{
    /*id	int(10)	否		ID
    user_id	int(10)	否		用户表ID
    company_name	varchar(50)	否		公司名称
    company_size	tinyint(4)	否		公司规模：1.初创；...
    company_web	varchar(50)	否		企业网址
    province	int(10)	否		省份
    city	int(10)	否		城市
    area	int(10)	否		地区
    address	varchar(50)	否		详细地址
    contact_name	varchar(20)	否		联系人姓名
    phone	varcahr(20)	否		手机
    email	varchar(50)	否		邮箱*/

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demand_company', function (Blueprint $table){
            $table->increments('id');
            $table->integer('user_id')->unique();
            $table->string('company_name', 50);
            $table->tinyInteger('company_size');
            $table->string('company_web',50);
            $table->integer('province');
            $table->integer('city');
            $table->integer('area');
            $table->string('address', 50);
            $table->string('contact_name', 20);
            $table->string('phone',20);
            $table->string('email', 50);
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
        Schema::dropIfExists('demand_company');
    }
}
