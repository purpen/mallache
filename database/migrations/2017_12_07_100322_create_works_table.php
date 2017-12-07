<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorksTable extends Migration
{

    /**
     * Run the migrations.
     * 大赛作品
     * @return void
     */
    public function up()
    {
        Schema::create('works', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('title' , 50);
            $table->integer('match_id')->default(0);
            $table->text('content')->nullable();
            $table->string('summary', 1000)->nullable();
            $table->string('tags', 100)->nullable();
            $table->integer('cover_id');
            $table->integer('view_count')->default(0);
            $table->tinyInteger('type')->default(1);
            $table->tinyInteger('published')->default(0);
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
        Schema::dropIfExists('works');
    }
}
