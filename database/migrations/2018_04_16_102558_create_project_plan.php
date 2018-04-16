<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectPlan extends Migration
{
    /**
     * quotation_id    int(10)    否        报价单ID
     * content    varchar(500)    否        工作内容
     * arranged    json    否        人员安排 (关联职位表)[1,2]
     * duration    int(10)    否        持续时间
     * price    decimal(10,2)    否        费用
     * summary    varchar(500)        null    备注
     */
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_plan', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('quotation_id');
            $table->string('content')->default('');
            $table->json('arranged');
            $table->integer('duration');
            $table->decimal('price', 10, 2);
            $table->string('summary')->nullable();

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
        Schema::dropIfExists('project_plan');
    }
}
