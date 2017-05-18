<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemStageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_stage', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id');
            $table->integer('design_company_id');
            $table->string('title');
            $table->string('content');
            $table->string('summary')->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_stage');
    }
}
