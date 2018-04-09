<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeModelIdToOperationLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('operation_log', function (Blueprint $table) {
            $table->dropColumn(['model_id', 'target_id']);
        });

        Schema::table('operation_log', function (Blueprint $table) {
            $table->integer('model_id')->nullable();
            $table->integer('target_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('operation_log', function (Blueprint $table) {
            //
        });
    }
}
