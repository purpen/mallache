<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDesignItemsTable extends Migration
{
    /*
        id	            int(10)	        否		ID
        user_id	        int(10)	        否		用户ID
        design_type	    tinyint(1)	    否		设计分类：1.产品设计；2.ux、ui设计
        product_type    tinyint(5)	    否		产品设计：1.产品策略；2.产品设计；3.结构设计；ux设计：1.app设计；2.网页设计；
        min_price	    decimal(10,2)	否		最低价格
        max_price	    decimal(10,2)	否		最高价格
     */
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('design_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->tinyInteger('company_type');
            $table->tinyInteger('design_type');
            $table->decimal('min_price' , 10 , 2);
            $table->decimal('max_price' , 10 , 2);
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
        Schema::dropIfExists('design_items');
    }
}
