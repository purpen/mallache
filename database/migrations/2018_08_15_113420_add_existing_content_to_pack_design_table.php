<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExistingContentToPackDesignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pack_design', function (Blueprint $table) {
            $table->dropColumn('existing_content');
        });
        Schema::table('pack_design', function (Blueprint $table) {
            $table->string('existing_content' , 200)->default('');
            $table->string('other_content' , 20)->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pack_design', function (Blueprint $table) {
            $table->dropColumn(['existing_content' , 'other_content']);
        });
        Schema::table('pack_design', function (Blueprint $table) {
            $table->tinyInteger('existing_content')->nullable();
        });
    }
}
