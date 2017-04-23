<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemRecommendTable extends Migration
{

    /*item_recommend 项目推荐关联表
    字段	类型	空	默认值	注释
    id	int(10)	否
    item_id	int(10)	否  项目ID
    item_status	int(10)	否	0	项目状态：-1.拒绝；1.选定设计公司；
    design_company_id	int(10)	否		设计公司id
    design_company_status	tinyint(4)	否	0	设计公司状态: -1.拒绝；1.一键成交；2.有意向报价;
    quotation_id	int(10)	是	0	报价单ID
    contract_id	int(10)	是	0	合同ID
    status	tinyint(4)	否	0	状态：-1.关闭；1.选定设计公司；2.提交合同；3.确定合同；4.已打款；5.项目开始；6.项目完成；7.已项目验收。8.付款*/
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_recommend', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id');
            $table->tinyInteger('item_status')->default(0);
            $table->integer('design_company_id');
            $table->tinyInteger('design_company_status')->default(0);
            $table->integer('quotation_id')->default(0);
            $table->integer('contract_id')->default(0);
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('item_recommend');
    }
}
