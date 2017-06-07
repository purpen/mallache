<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAmountToItemStageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_stage', function (Blueprint $table) {
            $table->string('content', 500)->default('')->change();
            $table->decimal('percentage', 10, 2)->default(0);
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('time', 20)->default('');
            $table->integer('sort')->defaualt(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_stage', function (Blueprint $table) {
            //
        });
    }
}
