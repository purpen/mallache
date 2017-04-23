<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotationsTable extends Migration
{
    /*
        id	                int(10)	        否
        item_demand_id	    int(10)	        否		项目需求ID
        design_company_id	int(10)	        否		设计公司id
        price	            decimal(10,2)	否		报价
        summary	            varchar(500)	否		报价说明
        status	            tinyint(10)	    否	0	状态：1.已提交
     */
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_demand_id');
            $table->integer('design_company_id');
            $table->decimal('price' , 10 , 2);
            $table->string('summary' , 500);
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('quotations');
    }
}
