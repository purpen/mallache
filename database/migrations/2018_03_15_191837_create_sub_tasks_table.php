<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubTasksTable extends Migration
{
    /**
    id	            int(10)	        否
    name	        varchar(100)	是		名称
    summary	        varchar(1000)	否		备注
    user_id	        int(11)	        是		创建人ID
    execute_user_id	int(11)	        否		执行人ID
    item_id	        int(11)	        是		所属项目ID
    task_id	        int(11)	        是		所属任务ID
    tags	        varchar(500)	否		标签
    type	        tinyint(1)	    是	1	类型;1.默认；
    stage	        tinyint(1)	    是	0	是否完成:0.未完成；2.已完成；
    over_time	    datetime	    否		完成时间
    status	        tinyint(1)	    是	1	状态：0.禁用；1.启用；
     */
    public function up()
    {
        Schema::create('sub_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name' , 100);
            $table->string('summary' , 1000);
            $table->string('tags' , 500);
            $table->integer('user_id')->default(0);
            $table->integer('execute_user_id')->default(0);
            $table->integer('item_id')->default(0);
            $table->integer('task_id')->default(0);
            $table->tinyInteger('type')->default(1);
            $table->tinyInteger('stage')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->date('over_time')->nullable();
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
        Schema::dropIfExists('sub_tasks');
    }
}
