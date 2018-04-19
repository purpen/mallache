<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateToDesignStage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * id    int(10)    否
         * design_project_id    int(10)            项目ID
         * name    varchar(100)            阶段名称
         * duration    int(10)            投入时间
         * start_time    int(10)            开始时间
         * content    varchar(500)    是        交付内容
         * user_id    int(10)            操作用户ID
         * status    tinyint(4)            状态 0.未完成 1.已完成
         */
        Schema::create('design_stage', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('design_project_id');
            $table->string('name', 100);
            $table->integer('duration')->nullable();
            $table->integer('start_time')->nullable();
            $table->string('content', 500)->default('');
            $table->integer('user_id');
            $table->tinyinteger('status')->default(0);
            $table->timestamps();
        });

        /**
         * id    int(10)    否
         * design_project_id    int(10)            项目ID
         * design_stage_id    int(10)            设计阶段ID
         * name    varchar(100)    否        名称
         * execute_user_id    int(10)            执行人 (跟任务是否同步)
         * duration    int(10)            投入时间
         * start_time    int(10)            开始时间
         * summary    varchar(500)            描述
         * stage_node_id    int(10)        null    节点ID
         * user_id    int(10)            操作用户ID
         * status    tinyint(4)            状态：0.未完成 1.完成
         */
        Schema::create('design_substage', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('design_project_id');
            $table->integer('design_stage_id');
            $table->string('name', 100);
            $table->integer('execute_user_id')->nullable();
            $table->integer('duration')->nullable();
            $table->integer('start_time')->nullable();
            $table->string('summary', 500)->default('');
            $table->integer('stage_node_id')->nullable();
            $table->integer('user_id');
            $table->tinyinteger('status')->default(0);

            $table->timestamps();
        });

        /**
         * id    int(10)    否
         * name    varchar(100)            名称
         * time    int(10)            截止时间
         * status    tinyint(4)            状态：1.未完成 2.已完成
         * is_owner    tinyint(4)            甲方参与：0.否 1.是
         * |design_project_id |int(10)|||项目ID|
         */
        Schema::create('stage_node', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name', 100);
            $table->integer('time');
            $table->tinyinteger('status')->default(0);
            $table->tinyinteger('is_owner')->default(0);
            $table->integer('design_project_id');
            $table->timestamps();
        });

        /**
         * id    int(10)
         * name    varchar(100)            节点名称
         * design_company_id    int(10)            设计公司ID
         * user_id    int(10)            创建人
         */
        Schema::create('nodes', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name', 100);
            $table->integer('design_company_id');
            $table->integer('user_id');

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
        Schema::dropIfExists('design_stage');
        Schema::dropIfExists('design_substage');
        Schema::dropIfExists('stage_node');
        Schema::dropIfExists('nodes');
    }
}
