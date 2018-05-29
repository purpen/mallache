<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommissionToContract extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * thn_company_name    varchar(50)    是    ''    平台名称
         * thn_company_address    varchar(50)    是    ''    平台地址
         * thn_company_phone    varchar(20)    是    ''    平台电话
         * thn_company_legal_person    varchar(20)    是    ''    平台联系人
         * commission    decimal(10,2)        0    佣金
         * commission_rate    tinyint(4)        0    佣金比例
         */
        Schema::table('contract', function (Blueprint $table) {
            $table->string('thn_company_name', 50)->default('');
            $table->string('thn_company_address', 50)->default('');
            $table->string('thn_company_phone', 20)->default('');
            $table->string('thn_company_legal_person', 20)->default('');
            $table->decimal('commission', 10, 2)->default(0);
            $table->tinyInteger('commission_rate')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contract', function (Blueprint $table) {
            $table->dropColumn(['thn_company_name', 'thn_company_address', 'thn_company_phone', 'thn_company_legal_person', 'commission', 'commission_rate']);
        });
    }
}
