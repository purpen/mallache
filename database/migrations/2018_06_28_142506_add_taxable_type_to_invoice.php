<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTaxableTypeToInvoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice', function (Blueprint $table) {
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
        Schema::table('invoice', function (Blueprint $table) {
            $table->dropColumn(['taxable_type', 'invoice_type']);
        });
    }
}
