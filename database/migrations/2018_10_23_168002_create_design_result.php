<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDesignResult extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('design_result', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title',30);
            $table->string('content',500);
            $table->integer('cover_id');
            $table->tinyInteger('sell_type');
            $table->decimal('price',8,2)->default(0);
            $table->tinyInteger('share_ratio')->default(0);
            $table->integer('design_company_id');
            $table->integer('user_id');
            $table->tinyInteger('status');
            $table->decimal('thn_cost',8,2)->default(0);
            $table->integer('follow_count')->default(0);
            $table->integer('demand_company_id');
            $table->integer('purchase_user_id');
            $table->softDeletes();
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
        Schema::dropIfExists('design_result');
    }
}
