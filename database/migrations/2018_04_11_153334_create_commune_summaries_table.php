<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommuneSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commune_summaries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id')->default(0);
            $table->integer('user_id')->default(0);
            $table->string('title' , 100);
            $table->string('content' , 1000);
            $table->string('location' , 100);
            $table->tinyInteger('status')->default(1);
            $table->date('expire_time')->nullable();
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
        Schema::dropIfExists('commune_summaries');
    }
}
