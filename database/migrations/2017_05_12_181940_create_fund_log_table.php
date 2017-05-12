<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFundLogTable extends Migration
{
    /*fund_log 资金流水记录
    字段	类型	空	默认值	注释
    id	int(10)	否
    user_id	int(10)	否		用户ID
    type	tinyint(4)	否		交易类型：-1：出账；1.入账
    transaction_type	tinyint(4)	否		交易对象类型： 1.平台用户；2.支付宝；3.微信；4：京东；
    target_id	varchar(50)	否	''	交易对象
    Amount	decimal(10.2)	否	0	交易金额
    summary	varchar(500)	是		备注*/
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fund_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->tinyInteger('type');
            $table->tinyInteger('transaction_type');
            $table->string('target_id', 50)->default('');
            $table->decimal('amount', 10, 2);
            $table->string('summary')->default('');
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
        Schema::dropIfExists('fund_log');
    }
}
