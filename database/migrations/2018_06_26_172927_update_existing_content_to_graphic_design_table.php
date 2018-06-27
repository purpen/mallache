<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateExistingContentToGraphicDesignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('h5_design', function (Blueprint $table) {
            $table->dropColumn('existing_content');
        });

        Schema::table('illustration_design', function (Blueprint $table) {
            $table->dropColumn('existing_content');
        });
        Schema::table('graphic_design', function (Blueprint $table) {
            $table->tinyInteger('existing_content')->default(0);
        });
        Schema::table('pack_design', function (Blueprint $table) {
            $table->tinyInteger('existing_content')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('graphic_design', function (Blueprint $table) {
            $table->dropColumn(['existing_content']);
        });
        Schema::table('pack_design', function (Blueprint $table) {
            $table->dropColumn(['existing_content']);
        });
    }
}
