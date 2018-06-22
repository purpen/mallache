<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateH5DesignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('h5_design', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id')->default(0);
            $table->tinyInteger('present_situation')->default(0);
            $table->tinyInteger('existing_content')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->string('product_features', 500)->default('');
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
        Schema::dropIfExists('h5_design');
    }
}
