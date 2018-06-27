<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLogisticsToInvoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * logistics_id    tinyint(4)        null    物流公司ID
         * logistics_number    varchar(50)        null    物流单号
         */
        Schema::table('invoice', function (Blueprint $table) {
            $table->tinyInteger('logistics_id')->nullable();
            $table->string('logistics_number', 50)->nullable();
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
            $table->dropColumn(['logistics_id', 'logistics_number']);
        });
    }
}
