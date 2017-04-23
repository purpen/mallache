<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductDesignsTable extends Migration
{
    /*product_design 产品设计表
    字段	类型	空	默认值	注释
    id	int(10)	否
    item_id	int(10)	否		项目ID
    name	varchar(50)	否		项目名称
    target_retail_price	decimal(10,2)	否		目标零售价格
    annual_sales	int(10)	否		年销量
    service_life	tinyint(4)	否		产品使用寿命:1.一次性；2.经常使用；3.产期使用；4.购买周期内
    competitive_brand	varchar(50)	否		竞争品牌
    competing_product	varchar(50)	否		竞品
    target_brand	varchar(50)	否		目标品牌
    brand_positioning	tinyint(4)	否		品牌定位：1.领导2.跟随3.高端4.中端5.低端
    product_size	varchar(50)	否		预期产品尺寸
    reference_model	varchar(50)	否		参考机型
    percentage_completion	tinyint	否		产品内部框架完成百分比：1.无；2.30%；3.50%；4.70%；5.100%;
    special_technical_require	int(10)	否		特殊技术要求 class_id
    design_cost	tinyint(4)	否		设计费用：
    province	int(10)	否		省份
    city	int(10)	否		城市
    divided_into_cooperation	tinyint(4)	否		是否考虑销售分成入股 1.否；2.是；
    stock_cooperation	tinyint(4)	否		是否考虑设计入股合作方式1.否；2.是
    summary	varchar(500)	否		备注*/

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_design', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id');
            $table->string('name', 50)->default('');
            $table->decimal('target_retail_price', 10, 2)->default(0);
            $table->integer('annual_sales')->default(0);
            $table->tinyInteger('service_life')->default(0);
            $table->string('competitive_brand',50)->default('');
            $table->string('competing_product', 50)->default('');
            $table->string('target_brand', 50)->default('');
            $table->tinyInteger('brand_positioning')->default(0);
            $table->string('product_size', 50)->default('');
            $table->string('reference_model', 50)->default('');
            $table->tinyInteger('percentage_completion')->default(0);
            $table->integer('special_technical_require')->default(0);
            $table->tinyInteger('design_cost')->default(0);
            $table->integer('province')->default(0);
            $table->integer('city')->default(0);
            $table->tinyInteger('divided_into_cooperation')->default(0);
            $table->tinyInteger('stock_cooperation')->default(0);
            $table->string('summary', 500)->default('');
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
        Schema::dropIfExists('product_design');
    }
}
