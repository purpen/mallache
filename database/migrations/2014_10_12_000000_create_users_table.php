<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
/*    id	int(10)	否		用户ID
    account	varchar(20)	否		用户名
    password	varchar(50)	否		密码
    username	varchar(50)	是		用户名
    email	varchar(50)	是		邮箱
    phone	varchar(20)	否		手机号码
    design_company_id	int(10)	否	0	设计公司ID
    status	tinyint(1)	否	0	状态：；-1：禁用；1.激活;2...
    item_sum	int(10)	否	0	项目数量
    price_total	decimal(10,2)	否	0	总金额
    price_frozen	decimal(10,2)	否	0	冻结金额
*/

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('account')->unique();
            $table->string('phone',20)->unique();
            $table->string('username',50)->default('');
            $table->string('email')->nullable();
            $table->string('password');
            $table->integer('design_company_id')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->integer('item_sum')->default(0);
            $table->decimal('price_total',10,2)->default(0);
            $table->decimal('price_frozen',10,2)->default(0);
            $table->rememberToken();
            $table->timestamps();
            $table->unique('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
