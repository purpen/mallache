<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDateToContractTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract', function (Blueprint $table) {
            $table->string('project_start_date' , 20)->change();
            $table->string('determine_design_date', 20)->change();
            $table->string('structure_layout_date', 20)->change();
            $table->string('design_sketch_date', 20)->change();
            $table->string('end_date', 20)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contract', function (Blueprint $table) {
            $table->integer('project_start_date')->default(0);
            $table->integer('determine_design_date')->default(0);
            $table->integer('structure_layout_date')->default(0);
            $table->integer('design_sketch_date')->default(0);
            $table->integer('end_date')->default(0);
        });
    }
}
