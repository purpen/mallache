<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDesignPosition extends Migration
{
    /**
     * name    varchar(50)    否        职位名称
     * design_company_id    int(10)    否        公司ID
     */
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('design_position', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->integer('design_company_id');
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
        Schema::dropIfExists('design_position');
    }
}
