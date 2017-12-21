<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAwardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('award_case', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 30);
            $table->text('content')->nullable();
            $table->string('summary', 500)->nullable();
            $table->string('images_url', 1000)->nullable();
            $table->string('grade', 100)->nullable();
            $table->string('url', 500)->nullable();
            $table->string('time_at', 100)->nullable();
            $table->integer('user_id')->default(0);
            $table->integer('cover_id')->default(0);
            $table->integer('category_id')->default(0);
            $table->tinyInteger('type')->default(1);
            $table->tinyInteger('recommended')->default(0);
            $table->integer('recommended_on')->default(0);
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('award_case');
    }
}
