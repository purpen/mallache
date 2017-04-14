<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompanyAbbreviationToDemandCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('demand_company', function (Blueprint $table) {
            $table->string('company_abbreviation', 50)->default('');
            $table->integer('city')->default(0)->change();
            $table->integer('area')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('demand_company', function (Blueprint $table) {
            $table->dropColumn(['company_abbreviation']);
        });
    }
}
