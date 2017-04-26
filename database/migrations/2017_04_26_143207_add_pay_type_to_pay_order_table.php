<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPayTypeToPayOrderTable extends Migration
{
    /*
    pay_type	tinyint(4)	是	0	支付方式；1.支付宝；2.微信；3.京东；
    pay_no	varchar(30)	是	’‘	对应平台支付交易号
    */
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pay_order', function (Blueprint $table) {
            $table->tinyInteger('pay_type')->default(0);
            $table->string('pay_no',30)->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pay_order', function (Blueprint $table) {
            $table->dropColumn(['pay_type', 'pay_no']);
        });
    }
}
