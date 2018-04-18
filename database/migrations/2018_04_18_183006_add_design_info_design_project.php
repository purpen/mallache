<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDesignInfoDesignProject extends Migration
{
    /**
     * quotation_id    int(10)        null    报价单ID
     * design_company_name    varchar(100)        ''    设计公司名称
     * design_contact_name    varchar(50)    是    ‘’    设计公司联系人姓名
     * design_phone    varcahr(20)        ''    设计公司手机
     * design_address    varchar(100)        'null    设计公司详细地址
     */
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('design_project', function (Blueprint $table) {
            $table->integer('quotation_id')->nullable();
            $table->string('design_company_name', 100)->default('');
            $table->string('design_contact_name', 50)->default('');
            $table->string('design_phone', 20)->default('');
            $table->string('design_address', 100)->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('design_project', function (Blueprint $table) {
            $table->dropColumn(['quotation_id', 'design_company_name', 'design_contact_name', 'design_phone', 'design_address']);
        });
    }
}
