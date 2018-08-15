<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DesignStatistics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('design_substage', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('design_company_id')->default(0);
            $table->integer('score')->default(0);
            $table->integer('jump_count')->default(0);
            $table->tinyInteger('level')->default(0);
            $table->decimal('average_price',2)->default(0);
            $table->integer('last_time')->default(0);
            $table->integer('recommend_count')->default(0);
            $table->integer('cooperation_count')->default(0);
            $table->float('success_rate',4,4)->default(0);
            $table->integer('case')->default(0);
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
        //
    }
}
