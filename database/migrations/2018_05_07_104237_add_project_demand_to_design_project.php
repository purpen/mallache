<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProjectDemandToDesignProject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('design_project', function (Blueprint $table) {
            $table->text('project_demand')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('design_project', function (Blueprint $table) {
            $table->dropColumn('project_demand');
        });
    }
}
