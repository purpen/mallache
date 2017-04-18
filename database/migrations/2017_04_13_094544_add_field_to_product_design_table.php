<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToProductDesignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_design', function (Blueprint $table) {
            $table->tinyInteger('field')->default(0);
            $table->tinyInteger('industry')->default(0);
            $table->string('product_features',500)->default('');
            $table->tinyInteger('cycle')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_design', function (Blueprint $table) {
            $table->dropColumn(['field','industry','product_features','cycle']);
        });
    }
}
