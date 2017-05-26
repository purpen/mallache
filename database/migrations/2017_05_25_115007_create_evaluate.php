<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEvaluate extends Migration
{
    /*id	int(10)	否
    demand_company_id	int(10)	否		需求公司ID
    design_company_id	int(10)	否		设计公司ID
    item_id	int(10)	否		项目ID
    content	varchar(500)	否		内容
    score	tinyint(4)	否		评分
    status	tinyint(4)	否	0	状态*/
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluate', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('demand_company_id');
            $table->integer('design_company_id');
            $table->integer('item_id');
            $table->string('content', 500);
            $table->tinyInteger('score');
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
        Schema::dropIfExists('evaluate');
    }
}
