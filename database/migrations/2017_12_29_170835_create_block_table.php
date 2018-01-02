<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('block', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('mark', 20);
            $table->tinyInteger('type')->default(1);
            $table->text('code')->nullable();
            $table->text('content')->nullable();
            $table->string('summary', 1000)->nullable();
            $table->integer('user_id')->default(0);
            $table->integer('count')->default(0);
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
        Schema::dropIfExists('block');
    }
}
