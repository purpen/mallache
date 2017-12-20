<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommonlyUsedUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commonly_used_urls', function (Blueprint $table) {
            $table->increments('id');
            $table->string('summary', 100);
            $table->string('url', 100);
            $table->string('title', 30);
            $table->tinyInteger('type')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->integer('cover_id')->default(0);
            $table->integer('user_id');
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
        Schema::dropIfExists('commonly_used_urls');
    }
}
