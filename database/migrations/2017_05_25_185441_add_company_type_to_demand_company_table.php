<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompanyTypeToDemandCompanyTable extends Migration
{
    /*company_type	tinyint(1)	否		企业类型：1.普通；2.多证合一（不含社会统一信用代码）；3.多证合一（含社会统一信用代码）
    registration_number	varchar(15)	否		注册号
    legal_person	varchar(20)	否		法人姓名
    document_type	tinyint(1)	否		法人证件类型：1.身份证；2.港澳通行证；3.台胞证；4.护照；
    document_number	varchar(20)	否		证件号码
    company_property	tinyint(4)	否		企业性质：1.初创企业、2.私企、3.国有企业、4.事业单位、5.外资、6.合资、7.上市公司*/
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('demand_company', function (Blueprint $table) {
            $table->tinyInteger('company_type')->default(0);
            $table->string('registration_number',15)->default('');
            $table->string('legal_person', 20)->default('');
            $table->tinyInteger('document_type')->default(0);
            $table->string('document_number', 20)->default('');
            $table->tinyInteger('company_property')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('demand_company', function (Blueprint $table) {
            //
        });
    }
}
