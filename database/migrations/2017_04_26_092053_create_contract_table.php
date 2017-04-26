<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractTable extends Migration
{
    /*
            item_demand_id	                    int(10)	    否		项目ID
            design_company_id	                int(10)	    否		设计公司ID
            demand_company_name	                string(50)	是	空	需求公司名称
            demand_company_address	            string(50)	是	空	需求公司地址
            demand_company_phone	            string(20)	是	空	需求公司电话
            demand_company_legal_person	        string(20)	是	空	需求公司法人
            design_company_name	                string(50)	是	空	设计公司名称
            design_company_address	            string(50)	是	空	设计公司地址
            design_company_phone	            string(20)	是	空	设计公司电话
            design_company_legal_person	        string(20)	是	空	设计公司法人
            design_type	                        string(20)	是	空	设计类型
            design_type_paragraph	            string(20)	是	空	设计类型几款
            design_type_contain	                string(20)	是	空	设计类型包含
            total	                            string(20)	是	空	总额
            project_start_date	                int(10)	    是	0	项目启动日期
            determine_design_date	            int(10)	    是	0	设计确定日期
            structure_layout_date	            int(10)	    是	0	结构布局验证日期
            design_sketch_date	                int(10)	    是	0	效果图日期
            end_date	                        int(10)	    是	0	最后确认日期
            one_third_total	                    string(20)	是	空	30%总额
            exterior_design_percentage	        int(10)	    是	0	外观设计百分比
            exterior_design_money	            string(20)	是	空	外观设计金额
            exterior_design_phase	            string(20)	是	空	外观设计阶段
            exterior_modeling_design_percentage	int(10)	    是	0	外观建模设计百分比
            exterior_modeling_design_money	    string(20)	是	空	外观建模设计金额
            design_work_content	                string(50)	是	空	设计工作内容
            status	                            tinyint(10)	否	0	合同状态.0可以修改.1不可以
     */
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_demand_id');
            $table->integer('design_company_id');
            $table->string('demand_company_name' , 50)->default('');
            $table->string('demand_company_address' , 50)->default('');
            $table->string('demand_company_phone' , 20)->default('');
            $table->string('demand_company_legal_person' , 20)->default('');
            $table->string('design_company_name' , 50)->default('');
            $table->string('design_company_address' , 50)->default('');
            $table->string('design_company_phone' , 20)->default('');
            $table->string('design_company_legal_person' , 20)->default('');
            $table->string('design_type' , 20)->default('');
            $table->string('design_type_paragraph' , 20)->default('');
            $table->string('design_type_contain' , 20)->default('');
            $table->string('total' , 20)->default('');
            $table->integer('project_start_date')->default(0);
            $table->integer('determine_design_date')->default(0);
            $table->integer('structure_layout_date')->default(0);
            $table->integer('design_sketch_date')->default(0);
            $table->integer('end_date')->default(0);
            $table->string('one_third_total' , 20)->default('');
            $table->integer('exterior_design_percentage')->default(0);
            $table->string('exterior_design_money' , 20)->default('');
            $table->string('exterior_design_phase' , 20)->default('');
            $table->integer('exterior_modeling_design_percentage')->default(0);
            $table->string('exterior_modeling_design_money' , 20)->default('');
            $table->string('design_work_content' , 50)->default('');
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
        Schema::dropIfExists('contract');
    }
}
