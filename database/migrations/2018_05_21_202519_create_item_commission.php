<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemCommission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_commission', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('design_user_id');
            $table->integer('item_id');
            $table->decimal('price', 10, 2);
            $table->decimal('commission', 10, 2);
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
        Schema::dropIfExists('item_commission');
    }
}
