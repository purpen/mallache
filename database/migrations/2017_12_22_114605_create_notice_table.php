<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNoticeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notice', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 50);
            $table->text('content')->nullable();
            $table->string('summary', 500)->nullable();
            $table->string('url', 500)->nullable();
            $table->integer('user_id')->default(0);
            $table->integer('cover_id')->default(0);
            $table->tinyInteger('type')->default(1);
            $table->tinyInteger('evt')->default(0);
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
        Schema::dropIfExists('notice');
    }
}
