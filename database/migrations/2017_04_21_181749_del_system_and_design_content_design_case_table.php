<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DelSystemAndDesignContentDesignCaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('design_case', function (Blueprint $table) {
            $table->dropColumn(['system' , 'design_content']);
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
            $table->tinyInteger('system')->default(0);
            $table->tinyInteger('design_content')->default(0);
        });
    }
}
