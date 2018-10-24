<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDesignDemandTable extends Migration
{
    /**
    id	                int(10)	        否
    user_id	            int(10)	        否		用户ID
    demand_company_id	int(10)			        需求公司ID
    status	            tinyint(4)	    否	0	状态：-1.关闭 0.待发布 1. 审核中 2.已发布
    type	            tinyint(4)	    否		设计类型：1.产品设计；2.UI UX 设计；3. 平面设计 4.H5 5.包装设计 6.插画设计
    design_types	    json	        是		设计类别：产品设计（1.产品策略；2.产品设计；3.结构设计 4.其他；[1,2]
    name	            varchar(100)	是		项目名称
    cycle	            tinyint(4)	    是		设计周期：1.1个月内；2.1-2个月；3.2-3个月；4.3-4个月；5.4个月以上
    design_cost	        tinyint(4)	    是		设计费用：1、1-5万；2、5-10万；3.10-20；4、20-30；5、30-50；6、50以上
    item_province	    int(10)	        是		省份
    item_city	        int(10)	        是		城市
    field	            tinyint(4)		    0	产品类别：'智能硬件','消费电子'等
    industry	        tinyint(4)	    否		所属行业 1.制造业,2 .消费零售,3 .信息技术,4 .能源,5 .金融地产,6 .服务业,7 .医疗保健,8 .原材料,9 .工业制品,10 .军工,11 .公用事业
    follow_count	    int(10)		        0	关注数量
    content	            text	        是		产品描述
     */

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('design_demand', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('demand_company_id');
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('type');
            $table->json('design_types')->nullable();
            $table->string('name', 100)->nullable();
            $table->tinyInteger('cycle')->nullable();
            $table->tinyInteger('design_cost')->nullable();
            $table->integer('item_province')->nullable();
            $table->integer('item_city')->nullable();
            $table->tinyInteger('field')->default(0);
            $table->tinyInteger('industry');
            $table->integer('follow_count')->default(0);
            $table->text('content')->nullable();
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
        Schema::dropIfExists('design_demand');
    }
}
