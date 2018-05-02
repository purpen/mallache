<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDesignNotice extends Migration
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
         * user_id    int(11)    否        用户ID
         * is_read    tinyint(4)    否        是否阅读 0.否 1.是
         * operation_log_id    int(10)    否        设计工具动态ID
         */
        Schema::create('design_notice', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id');
            $table->tinyInteger('is_read')->default(0);
            $table->integer('operation_log_id');

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
        Schema::dropIfExists('design_notice');
    }
}
