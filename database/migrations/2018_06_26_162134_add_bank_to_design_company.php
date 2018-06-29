<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBankToDesignCompany extends Migration
{

    /*
    account_name	varchar(30)	否		开户名
    bank_name	varchar(30)	否		开户支行名称
    account_number	varchar(50)	否		银行账号
    taxable_type	tinyint(4)		null	纳税类型 1. 一般纳税人 2. 小额纳税人
    invoice_type	tinyint(4)		null	发票类型 1. 专票 2. 普票
    */
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('design_company', function (Blueprint $table) {
            $table->string('account_name', 30)->default('');
            $table->string('bank_name', 30)->default('');
            $table->string('account_number', 50)->default('');
            $table->tinyInteger('taxable_type')->nullable();
            $table->tinyInteger('invoice_type')->nullable();
        });


        Schema::table('demand_company', function (Blueprint $table) {
            $table->string('account_name', 30)->default('');
            $table->string('bank_name', 30)->default('');
            $table->string('account_number', 50)->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('design_company', function (Blueprint $table) {
            $table->dropColumn(['account_name', 'bank_name', 'account_number', 'taxable_type', 'invoice_type']);
        });

        Schema::table('demand_company', function (Blueprint $table) {
            $table->dropColumn(['account_name', 'bank_name', 'account_number']);
        });
    }
}
