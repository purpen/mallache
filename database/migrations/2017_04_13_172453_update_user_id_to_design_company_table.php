<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserIdToDesignCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('design_company', function (Blueprint $table) {
            $table->string('design_type',50)->default('')->change();
            $table->string('company_abbreviation', 50);
        });
    }

    public function down()
    {
        Schema::table('design_company', function (Blueprint $table) {
            $table->dropColumn('company_abbreviation');
        });
    }

}
