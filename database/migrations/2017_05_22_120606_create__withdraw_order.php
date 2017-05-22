<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWithdrawOrder extends Migration
{

    /*id	int(10)	否
    uid	varchar(50)	否		单号
    user_id	int(10)	否		用户ID
    type	tinyint(4)	否		类型：1.银行转账；
    amount	decimal(10,2)	否		提现金额
    company_name	varchar(30)	否		公司名称
    account_name	varchar(30)	否		开户名
    account_number	varchar(50)	否		账号
    bank_id	tinyint(4)	是	0	开户行
    branch_name	varchar(30)	是	‘’	支行名称
    status	tinyint(4)	否	0	状态：0.申请；1.同意；
    summary	varchar(200)	是	‘‘	备注
*/
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdraw_order', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->integer('user_id');
            $table->tinyInteger('type');
            $table->decimal('amount', 10, 2);
            $table->string('company_name', 30);
            $table->string('account_name', 30);
            $table->string('account_number', 50);
            $table->tinyInteger('bank_id')->default(0);
            $table->string('branch_name', 30)->default('');
            $table->tinyInteger('status')->default(0);
            $table->string('summary', 200)->default('');
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
        Schema::dropIfExists('withdraw_order');
    }
}
