<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOtherCompanyNameToContract extends Migration
{
    /**
     * other_company_name    varchar(50)    是    ''    第三合作方名称
     * other_company_address    varchar(50)    是    ''    第三合作方地址
     * other_company_phone    varchar(20)    是    ''    第三合作方电话
     * other_company_legal_person    varchar(20)    是    ''    第三合作方联系人
     */
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract', function (Blueprint $table) {
            $table->string('other_company_name', 50)->default('');
            $table->string('other_company_address', 50)->default('');
            $table->string('other_company_phone', 20)->default('');
            $table->string('other_company_legal_person', 20)->default('');
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
            $table->dropColumn(['other_company_name', 'other_company_address', 'other_company_phone', 'other_company_legal_person']);
        });
    }
}
