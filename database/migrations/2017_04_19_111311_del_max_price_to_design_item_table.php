<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DelMaxPriceToDesignItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('design_item', function (Blueprint $table) {
            $table->dropColumn(['max_price']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('design_item', function (Blueprint $table) {
            $table->decimal('max_price' , 10 , 2);
        });
    }
}
