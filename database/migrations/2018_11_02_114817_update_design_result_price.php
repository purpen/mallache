<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDesignResultPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('design_result', function (Blueprint $table) {
            $table->decimal('price',10,2)->default(0)->change();
            $table->decimal('thn_cost',10,2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('design_result', function (Blueprint $table) {
            $table->decimal('price',8,2)->change();
            $table->decimal('thn_cost',8,2)->change();
        });
    }
}
