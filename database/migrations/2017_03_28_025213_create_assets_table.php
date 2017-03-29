<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('target_id')->nullable();
            $table->tinyInteger('type')->default(1);
            $table->string('name',50);
            $table->string('summary',100)->nullable();
            $table->string('path',100);
            $table->integer('size')->default(0);
            $table->integer('width')->default(0);
            $table->integer('height')->default(0);
            $table->string('mime',10);
            $table->string('domain',10);
            $table->tinyInteger('status')->default(1);
            $table->string('random',30);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assets');
    }
}
