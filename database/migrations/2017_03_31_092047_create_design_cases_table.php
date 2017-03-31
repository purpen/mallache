<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDesignCasesTable extends Migration
{
    /*
        id	            int(10)	        否		ID
        user_id	        int(10)	        否		用户ID
        title	        varchar(50)	    否		标题
        prize	        tinyint(4)	    是		奖项
        prize_time	    date	        是		获奖时间
        mass_production	tinyint(4)	    否		是否量产
        sales_volume	decimal(10,2)	是		销售金额
        customer	    varchar(50)	    否		服务客户
        product_type	tinyint(4)	    否		所属领域
        profile	        varchar(500)	否		项目描述
        status	        tinyint(4)	    否		状态
     */
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('design_cases', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('title' , 50);
            $table->tinyInteger('prize')->nullable();
            $table->date('prize_time')->nullable();
            $table->tinyInteger('mass_production');
            $table->decimal('sales_volume' , 10 , 2)->nullable();
            $table->string('customer' , 50);
            $table->tinyInteger('product_type');
            $table->string('profile' , 500);
            $table->tinyInteger('status');
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
        Schema::dropIfExists('design_cases');
    }
}
