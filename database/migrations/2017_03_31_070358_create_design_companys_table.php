<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDesignCompanysTable extends Migration
{
    /*
        id	                int(10)	    否		ID
        user_id	            int(10)	    否		用户表ID
        company_type	    tinyint(1)	否		企业类型：1.普通；2.多证合一；
        company_name	    varchar(50)	否		公司名称
        registration_number	varchar(15)	否		注册号
        province	        int(10)	    否		省份
        city	            int(10)	    否		城市
        area	            int(10)	    否		地区
        address	            varchar(50)	否		详细地址
        contact_name	    varchar(20)	否		联系人姓名
        position	        varchar(20)	否		职位
        phone	            varchar(20)	否		手机
        email	            varchar(50)	否		邮箱
        company_size	    tinyint(1)	是		公司规模：1.10以下；2.10-50；3.50-100；4.100以上;
        branch_office	    tinyint(1)	是	0	分公司：1.有；2.无；
        item_quantity	    tinyint(1)	是	0	服务项目：1.10以下；2.10-50；3.50-100；4.100-200;5.200以上
        good _field	        varchar(50)	否		擅长领域 class_id
        web	                varchar(50)	否		设计公司网站
        company_profile	    varchar(500)	是		公司简介
     */
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('design_companys', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->tinyInteger('company_type');
            $table->string('company_name' ,  50);
            $table->string('registration_number' ,15);
            $table->integer('province');
            $table->integer('city');
            $table->integer('area');
            $table->string('address' , 50);
            $table->string('contact_name' , 20);
            $table->string('position' , 20);
            $table->string('phone' , 20);
            $table->string('email' , 50);
            $table->tinyInteger('company_size')->nullable();
            $table->tinyInteger('branch_office')->default(0);
            $table->tinyInteger('item_quantity')->default(0);
            $table->string('company_profile' , 500)->nullable();
            $table->string('good _field' , 50);
            $table->string('web' , 50);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('design_companys');
    }
}
