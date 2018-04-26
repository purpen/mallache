<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDesignTypeToDesignProject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('design_project', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('design_project', function (Blueprint $table) {
            $table->tinyInteger('type')->nullable();
            $table->tinyInteger('project_type');
            $table->json('design_types')->nullable();
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
            //
        });
    }
}
