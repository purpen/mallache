<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankTable extends Migration
{
    /**
            id	            int(10)	        否
            user_id	        int(10)	        否		用户ID
            account_name	varchar(30)	    否		开户名
            bank_id	        tinyint(4)	    否		开户行
            branch_name	    varchar(30)	    否		支行名称
            account_number	varchar(50)	    否		银行账号
            province	    int(10)	        否		省份
            city	        int(10)	        否		城市
            status	        tinyint(4)	    否	0	状态: -1.删除；0.正常
            summary	        varchar(200)	是	“”	备注
     */
    public function up()
    {
        Schema::create('bank', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('account_name', 30);
            $table->tinyInteger('bank_id');
            $table->string('branch_name', 30);
            $table->string('account_number', 50);
            $table->integer('province');
            $table->integer('city');
            $table->tinyInteger('status')->default(0);
            $table->string('summary' , 200)->default('');
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
        Schema::dropIfExists('bank');
    }
}
