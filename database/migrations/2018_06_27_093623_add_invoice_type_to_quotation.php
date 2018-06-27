<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInvoiceTypeToQuotation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
        taxable_type	tinyint(4)		null	纳税类型 1. 一般纳税人 2. 小额纳税人
        invoice_type	tinyint(4)		null	发票类型 1. 专票 2. 普票
        */
        Schema::table('quotation', function (Blueprint $table) {
            $table->tinyInteger('taxable_type')->nullable();
            $table->tinyInteger('invoice_type')->nullable();
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
            $table->dropColumn(['taxable_type', 'taxable_type']);
        });
    }
}
