<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVerifySummaryToDesignCompany extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('design_company', function (Blueprint $table) {
            $table->string('verify_summary', 500)->default('');
        });

        Schema::table('demand_company', function (Blueprint $table) {
            $table->string('verify_summary', 500)->default('');
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
            $table->dropColumn(['verify_summary']);
        });

        Schema::table('demand_company', function (Blueprint $table) {
            $table->dropColumn(['verify_summary']);
        });
    }
}
