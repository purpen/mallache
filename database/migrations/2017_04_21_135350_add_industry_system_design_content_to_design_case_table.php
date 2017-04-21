<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndustrySystemDesignContentToDesignCaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('design_case', function (Blueprint $table) {
            $table->tinyInteger('type')->default(0);
            $table->tinyInteger('design_type')->default(0);
            $table->tinyInteger('industry')->default(0);
            $table->tinyInteger('system')->default(0);
            $table->tinyInteger('design_content')->default(0);
            $table->string('other_prize' , 30)->default('');
            $table->dropColumn('sales_volume');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('design_case', function (Blueprint $table) {
            $table->dropColumn(['type' , 'design_type' , 'industry' , 'system' , 'design_content' , 'other_prize']);
            $table->decimal('sales_volume' , 10 , 2)->nullable();
        });
    }
}
