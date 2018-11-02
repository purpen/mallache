<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDesignResultContactNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('design_result', function (Blueprint $table) {
            $table->string('contacts', 30)->default('')->change();
            $table->string('contact_number', 30)->default('')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('design_result', function (Blueprint $table) {
            $table->integer('contact_number')->change();
            $table->string('contacts',30)->change();
        });
    }
}
