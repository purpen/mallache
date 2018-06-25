<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTaskCountToDesignProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('design_project', function (Blueprint $table) {
            $table->integer('task_count')->default(0);
            $table->integer('task_ok_count')->default(0);
            $table->integer('task_no_count')->default(0);
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
            $table->dropColumn(['task_count' , 'task_ok_count' , 'task_no_count']);
        });
    }
}
