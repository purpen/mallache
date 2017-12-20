<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToTrendReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trend_reports', function (Blueprint $table) {
            $table->tinyInteger('status')->default(0);
            $table->string('summary');
            $table->integer('pdf_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trend_reports', function (Blueprint $table) {
            $table->dropColumn(['status' , 'summary' , 'pdf_id']);

        });
    }
}
