<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeToColumnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('column', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->string('title', 100);
            $table->string('content', 500)->change();
            $table->string('url', 200)->default('')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('column', function (Blueprint $table) {
            //
        });
    }
}
