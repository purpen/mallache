<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemQuotationTable extends Migration
{
    /*
        id  int(10) 否       ID
        user_id int(10) 否       用户ID
        type    tinyint(4) 否       设计类型：1.产品设计；2.UI UX 设计；3. 平面设计 4.H5 5.包装设计 6.插画设计
        design_types    json 是       
        level   tinyint(4) 否       级别 ：1. 一般 2. 较高 3.优质
        status   tinyint(4) 否   0  状态
     */


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_quotation', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->tinyInteger('type');
            $table->json('design_types')->nullable();
            $table->tinyInteger('level');
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('item_quotation');
    }
}
