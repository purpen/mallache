<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNewDesignCompany extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('design_company', function (Blueprint $table) {
            $table->dropColumn(['revenue','investment_product','company_english','weixin_id']);
        });

        Schema::table('design_company', function (Blueprint $table) {
            $table->string('company_english', 100)->default('');
            $table->string('weixin_id', 100)->default('');
            $table->tinyInteger('revenue')->nullable();
            $table->tinyInteger('investment_product')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('design_company', function (Blueprint $table) {
            //
        });
    }
}
