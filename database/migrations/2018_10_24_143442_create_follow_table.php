<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFollowTable extends Migration
{
    /*
     *  id	                int(10)	否
     *  type	            tinyint			类型：1. 设计需求 2.设计成果
     *  design_demand_id	int(10)			设计需求ID
     *  design_company_id	int(10)			设计公司ID
     *  design_result_id	int(10)			设计成果ID
     *  demand_company_id	int(10)			需求公司ID
    */

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('follow', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type');
            $table->integer('design_demand_id');
            $table->integer('design_company_id');
            $table->integer('design_result_id');
            $table->integer('demand_company_id');
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
        Schema::dropIfExists('follow');
    }
}
