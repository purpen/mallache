<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOperationLog extends Migration
{
    /**
     * operation_log
     * id
     * company_id：公司ID
     * type ：模块类型 1.项目 2.网盘
     * model_id: 模块ID （与type配合使用）
     * action_type : 动作类型
     * target_id: 目标ID（与action_type配合使用）：
     * user_id:操作人ID
     * other_user_id: 被操作人ID
     * content：变更内容。
     */
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operation_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->tinyInteger('type');
            $table->tinyInteger('model_id')->nullable();
            $table->tinyInteger('action_type');
            $table->tinyInteger('target_id');
            $table->integer('user_id');
            $table->integer('other_user_id')->nullable();
            $table->string('content', 500)->default('');
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
        Schema::dropIfExists('operation_log');
    }
}
