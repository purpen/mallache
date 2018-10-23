<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSummaryToSmallItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('small_items', function (Blueprint $table) {
            $table->tinyInteger('is_ok')->default(0);
            $table->integer('design_company_id')->default(0);
            $table->string('summary', 200)->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('small_items', function (Blueprint $table) {
            $table->dropColumn(['is_ok' , 'summary']);
        });
    }
}
