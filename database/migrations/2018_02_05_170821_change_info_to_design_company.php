<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeInfoToDesignCompany extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*company_english	varchar(100)	是	‘’	公司英文名
        revenue	tinyint	否		公司营收 1.100万以下 2.100-500万 3.500-1000万 4.1000-2000万 5.3000-5000万 6.5000万以上
        weixin_id	varchar(100)	是		微信公众号ID
        high_tech_enterprises	json	是		高新企业：1.市级；2.省级；3.国家级 [{'time': '2018-1-1','type': 1}]
        Industrial_design_center	json	是		工业设计中心：1.市级；2.省级；3.国家级 [{'time': '2018-1-1','type': 1}]
        investment_product	tinyint(4)	是		投资孵化产品 0.无；1.有；
        own_brand	json	是		自有产品品牌*/
        Schema::table('design_company', function (Blueprint $table) {
            $table->string('company_english', 100);
            $table->tinyInteger('revenue');
            $table->string('weixin_id', 100);
            $table->json('high_tech_enterprises');
            $table->json('Industrial_design_center');
            $table->tinyInteger('investment_product');
            $table->json('own_brand');
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
            $table->dropColumn(['company_english', 'revenue', 'weixin_id', 'high_tech_enterprises', 'Industrial_design_center', 'investment_product', 'own_brand']);
        });
    }
}
