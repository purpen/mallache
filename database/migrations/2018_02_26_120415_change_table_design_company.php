<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTableDesignCompany extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('design_case', function (Blueprint $table) {
            $table->dropColumn(['patent','prizes']);
        });
        Schema::table('design_company', function (Blueprint $table) {
            $table->dropColumn(['high_tech_enterprises','Industrial_design_center','own_brand']);
        });

        Schema::table('design_case', function (Blueprint $table) {
            $table->json('patent')->nullable();
            $table->json('prizes')->nullable();
        });
        Schema::table('design_company', function (Blueprint $table) {
            $table->json('high_tech_enterprises')->nullable();
            $table->json('Industrial_design_center')->nullable();
            $table->json('own_brand')->nullable();
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
