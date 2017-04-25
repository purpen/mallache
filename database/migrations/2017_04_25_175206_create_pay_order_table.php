<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayOrderTable extends Migration
{
    /*pay_order 支付单
    字段	类型	空	默认值	注释
    id	int(10)	否
    uid	string(20)	否		支付单号（唯一索引）
    user_id	int(10)	否		用户ID
    type	tinyint(4)	否		支付类型：1.预付押金；2.项目款；
    item_id	int(10)	是	0	目标ID
    status	tinyint(4)	否	0	状态：0.未支付；1.支付成功；
*/
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_order', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid',20)->unique();
            $table->integer('user_id')->default(0);
            $table->tinyInteger('type');
            $table->integer('item_id')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->string('summary',100)->default('');
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
        Schema::dropIfExists('pay_order');
    }
}
