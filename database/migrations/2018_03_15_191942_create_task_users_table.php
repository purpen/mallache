<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskUsersTable extends Migration
{
    /**
    id	    int(10)	    否
    task_id	int(11)	    是		所属任务ID
    user_id	int(11)	    是		所属用户ID
    type	tinyint(1)	是	1	类型;1.默认；
    status	tinyint(1)	是	1	状态：0.禁用；1.启用；
     */
    public function up()
    {
        Schema::create('task_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('task_id')->default(0);
            $table->integer('user_id')->default(0);
            $table->tinyInteger('type')->default(1);
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('task_users');
    }
}
