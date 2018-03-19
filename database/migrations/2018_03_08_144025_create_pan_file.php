<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePanFile extends Migration
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
         * name    varchar(100)    否        文件名称
         * md5    varchar(50)    是        文件MD5值
         * size    int(10)    否        文件大小
         * width    int(10)        null    宽
         * height    int(10)        null    高
         * mime_type    varchar(50)    否        资源类型
         * url    varchar(200)    否        资源url
         * user_id    int(10)    否        上传用户ID
         * count    int(10)    否    0    引用数量
         * status    tinyint(4)    否    0    状态：0.正常 1.禁用
         */
        Schema::create('pan_file', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('md5', 50)->nullable();
            $table->integer('size');
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('mime_type', 50);
            $table->string('url', 200);
            $table->integer('user_id');
            $table->integer('count')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->timestamps();

            $table->unique('md5');
        });


        /**
         * id    int(10)    否
         * company_id    int(10)    否    0    公司ID
         * pan_director_id    int(10)    否    0    上级目录ID：0：顶层文件
         * type    tinyint(4)    否        类型：1.文件夹、2.文件
         * name    varchar(100)    否        文件、目录名称
         * size    int(10)    否        文件大小
         * sort    int(10)    否    0    排序
         * mime_type    varchar(50)    否        资源类型
         * pan_file_id    int(10)    否    0    源文件ID：file_id 源文件表ID
         * user_id    int(10)    否        上传用户
         * group_id    int(10)    否    0    所属项目组ID 需要项目人员管理模块协同
         * open_set    tinyint(4)    否        隐私设置：1.公开 2.私有
         * status    tinyint(4)    否        文件状态：1.正常 2.删除中
         * tag    varchar(100)    是        标签
         */
        Schema::create('pan_director', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->default(0);
            $table->integer('pan_director_id');
            $table->tinyInteger('type');
            $table->string('name', 100);
            $table->integer('size');
            $table->integer('sort')->default(0);
            $table->string('mime_type', 50);
            $table->integer('pan_file_id');
            $table->integer('user_id');
            $table->integer('group_id');
            $table->tinyInteger('open_set');
            $table->tinyInteger('status');
            $table->string('tag', 100)->nullable();
            $table->timestamps();

            $table->index(['status','open_set','pan_director_id','company_id','user_id'], 'select_index');
        });

        /**
         * id    int(10)    否
         * pan_director_id    int(10)    否        文件层级表ID
         * user_id    int(10)    否        用户ID
         * type    tinyint(4)    否        分享类型 1.私密分享 2.公开
         * url_code    varchar(100)    否        分享唯一编码
         * password    varchar(10)    是    null    查看密码
         */
        Schema::create('pan_share', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pan_director_id');
            $table->integer('user_id');
            $table->tinyInteger('type');
            $table->string('url_code', 100);
            $table->string('password', 10)->nullable();
            $table->timestamps();

            $table->unique('url_code');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pan_file');
        Schema::dropIfExists('pan_director');
        Schema::dropIfExists('pan_share');

    }
}
