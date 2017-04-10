<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDesignItemsTable extends Migration
{
    /*
        id	            int(10)	        否		ID
        user_id	        int(10)	        否		用户ID
        good_field	    int(10)	        否		擅长领域 class_id
        project_cycle    tinyint(5)	    否		项目周期
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
            $table->integer('good_field');
            $table->tinyInteger('project_cycle');
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
