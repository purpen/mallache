<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeToQuotation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    /**
     * is_tax    tinyint(4)            是否含税 1.是 2.否
     * is_invoice is_tax    tinyint(4)    是        是否开发票 1.是 2.否
     * total_price    decimal(10,2)    否        合计金额
     * tax_rate    int(10)    是        税率
     * type    tinyint(4)    否        类型：0.线上 1.线下
     */
    public function up()
    {
        Schema::table('quotation', function (Blueprint $table) {
            $table->tinyInteger('is_tax');
            $table->tinyInteger('is_invoice')->nullable();
            $table->decimal('total_price', 10, 2)->default(0);
            $table->integer('tax_rate')->nullable();
            $table->tinyInteger('type')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quotation', function (Blueprint $table) {
            $table->dropColumn(['is_tax', 'is_invoice', 'total_price', 'tax_rate', 'type']);
        });
    }
}
