<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeToArticle extends Migration
{
//    type	tinyint(4)	no	1	类型：1.文章；2.专题；
//    topic_url	varchar(100)	yes		专题url
//    user_id	int(11)	no		创建人ID
//    label	varchar(100)	yes		标签
//    short_content	vachar(200)	yes		文章提要
//    source_from	varchar(50)	yes		文章来源

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('article', function (Blueprint $table) {
            $table->tinyInteger('type')->default(1);
            $table->string('topic_url', 100)->default('');
            $table->integer('user_id');
            $table->string('label', 100)->default('');
            $table->string('short_content', 200)->default('');
            $table->string('source_from', 50)->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('article', function (Blueprint $table) {
            $table->dropColumn(['type', 'topic_url', 'user_id', 'label', 'short_content', 'source_from']);
        });
    }
}
