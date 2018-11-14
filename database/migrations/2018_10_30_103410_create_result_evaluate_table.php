<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultEvaluateTable extends Migration
{
    /*
     *  id	int(10)	否
        design_company_id	int(10)	    否		设计公司ID
        design_result_id	int(10)	    否		设计成果ID
        demand_company_id	int(10)	    否		需求公司ID
        design_level	    int(10)	    是	    设计水平
        response_speed	    int(10)	    是	    响应速度
        serve_attitude	    int(10)	    是  	    服务态度
        content	            text	    是		评价内容
    */

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('result_evaluate', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('design_company_id');
            $table->integer('design_result_id');
            $table->integer('demand_company_id');
            $table->integer('design_level')->nullable();
            $table->integer('response_speed')->nullable();
            $table->integer('serve_attitude')->nullable();
            $table->text('content')->nullable();
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
        Schema::dropIfExists('result_evaluate');
    }
}
