<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDesignToItemTable extends Migration
{
    /*design_company_id	int(10)	否	0	设计公司id
contract_id	int(10)	是	0	合同ID
status	tinyint(4)	否	0	状态：-2.无设计接单关闭；-1.用户关闭；1.填写资料；2.人工干预；3.推荐；4.已推送设计公司；5.已选定设计公司；6.已提交合同；7.已确定合同；4.已打款；5.项目已开始；6.项目已完成；7.已项目验收。8.已付款*/
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item', function (Blueprint $table) {
            $table->integer('design_company_id')->default(0);
            $table->integer('contract_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item', function (Blueprint $table) {
            $table->dropColumn(['design_company_id', 'contract_id']);
        });
    }
}
