<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
    id	                int(10)	        否
    name	            varchar(100)	是		名称
    summary	            varchar(1000)	否		备注
    user_id	            int(11)	        是		创建人ID
    execute_user_id	    int(11)	        否		执行人ID
    item_id	            int(11)	        是		所属项目ID
    tags	            varchar(500)	否		标签
    level	            tinyint(1)	    是	1	优先级：1.普通；5.紧级；8.非常紧级；
    type	            tinyint(1)	    是	1	类型;1.默认；
    sub_count	        int(11)	        否	0	子项目数量
    sub_finfished_count	int(11)	        否	0	子项目完成数量
    love_count	        int(11)	        否	0	点赞数量
    collection_count	int(11)	        否	0	收藏数量
    stage	            tinyint(1)	    是	0	是否完成:0.未完成；1.进行中；2.已完成；
    start_time	        datetime	    否		开始时间
    over_time	        datetime	    否		完成时间
    status	            tinyint(1)	    是	1	状态：0.禁用；1.启用；
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name' , 100);
            $table->string('summary' , 1000);
            $table->string('tags' , 500);
            $table->integer('user_id')->default(0);
            $table->integer('execute_user_id')->default(0);
            $table->integer('item_id')->default(0);
            $table->integer('sub_count')->default(0);
            $table->integer('sub_finfished_count')->default(0);
            $table->integer('love_count')->default(0);
            $table->integer('collection_count')->default(0);
            $table->tinyInteger('level')->default(1);
            $table->tinyInteger('type')->default(1);
            $table->tinyInteger('stage')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->date('start_time')->nullable();
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
        Schema::dropIfExists('tasks');
    }
}
