<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoice extends Migration
{

    /**
     * id    int(10)    否
     * type    tinyint(4)    否        收发类型：1. 收 2. 开
     * pay_type    tinyint(4)    否        支付类型： 1. 首付款 2. 阶段款
     * company_type    tinyint(4)    否        公司类型 1. 需求公司 2. 设计公司
     * target_id    int(11)    否        公司ID
     * company_name    varcahr(100)        ''    公司名称
     * duty_number    varchar(50)        ''    税号
     * price    decimal(10,2)            金额
     * item_id    int(11)            项目ID
     * item_stage_id    int(11)            项目阶段
     * user_id    int(11)    no        操作用户
     * summary    varchar(500)    yes        备注
     */
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice', function (Blueprint $table) {
            $table->increments('id');

            $table->tinyInteger('type');
            $table->tinyInteger('pay_type');
            $table->tinyInteger('company_type');
            $table->integer('target_id');
            $table->string('company_name', 100)->default('');
            $table->string('duty_number', 100)->default('');
            $table->decimal('price', 10, 2);
            $table->integer('item_id');
            $table->integer('item_stage_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('summary', 500)->default('');
            $table->tinyInteger('status');
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
        Schema::dropIfExists('invoice');
    }
}
