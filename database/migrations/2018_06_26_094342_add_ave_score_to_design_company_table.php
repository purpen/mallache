<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAveScoreToDesignCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('design_company', function (Blueprint $table) {
            $table->integer('ave_score')->default(0);
            $table->integer('base_average')->default(0);
            $table->integer('credit_average')->default(0);
            $table->integer('business_average')->default(0);
            $table->integer('design_average')->default(0);
            $table->integer('effect_average')->default(0);
            $table->integer('innovate_average')->default(0);
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
            $table->dropColumn([
                'ave_score' ,
                'base_average' ,
                'credit_average',
                'business_average',
                'design_average',
                'effect_average',
                'innovate_average',
            ]);
        });
    }
}
