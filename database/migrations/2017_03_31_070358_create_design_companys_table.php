<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDesignCompanysTable extends Migration
{
    /*
        id	                int(10)	    否		ID
        user_id	            int(10)	    是		用户表ID
        company_type	    tinyint(1)	是		企业类型：1.普通；2.多证合一；
        company_name	    varchar(50)	是		公司名称
        registration_number	varchar(15)	是		注册号
        province	        int(10)	    是		省份
        city	            int(10)	    是		城市
        area	            int(10)	    是		地区
        address	            varchar(50)	是		详细地址
        contact_name	    varchar(20)	是		联系人姓名
        position	        varchar(20)	是		职位
        phone	            varchar(20)	是		手机
        email	            varchar(50)	是		邮箱
        company_size	    tinyint(1)	是		公司规模：1.10以下；2.10-50；3.50-100；4.100以上;
        branch_office	    tinyint(1)	是	0	分公司：1.有；2.无；
        item_quantity	    tinyint(1)	是	0	服务项目：1.10以下；2.10-50；3.50-100；4.100-200;5.200以上
        good _field	        varchar(50)	是		擅长领域 class_id
        web	                varchar(50)	是		设计公司网站
        company_profile	    varchar(500)	是		公司简介
        design_type	        varchar(50)	否		产品设计：1.产品策略；2.产品设计；3.结构设计；ux设计：4.app设计；5.网页设计；
        establishment_time	date	    是		公司成立时间
        professional_advantage	varchar(500)	是		专业优势
        awards	            varchar(500)	是		荣誉奖项
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
            $table->string('design_type' ,  50);
            $table->integer('user_id')->nullable();
            $table->tinyInteger('company_type')->nullable();
            $table->string('company_name' ,  50)->nullable();
            $table->string('registration_number' ,15)->nullable();
            $table->integer('province')->nullable();
            $table->integer('city')->nullable();
            $table->integer('area')->nullable();
            $table->string('address' , 50)->nullable();
            $table->string('contact_name' , 20)->nullable();
            $table->string('position' , 20)->nullable();
            $table->string('phone' , 20)->nullable();
            $table->string('email' , 50)->nullable();
            $table->tinyInteger('company_size')->nullable();
            $table->tinyInteger('branch_office')->nullable();
            $table->tinyInteger('item_quantity')->nullable();
            $table->string('company_profile' , 500)->nullable();
            $table->string('good_field' , 50)->nullable();
            $table->string('web' , 50)->nullable();
            $table->date('establishment_time')->nullable();
            $table->string('professional_advantage' , 500)->nullable();
            $table->string('awards' , 500)->nullable();
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
