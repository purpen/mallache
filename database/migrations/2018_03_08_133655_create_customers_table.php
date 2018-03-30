<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_name', 50);
            $table->string('address', 50);
            $table->string('contact_name', 20);
            $table->string('phone', 20);
            $table->string('position', 20);
            $table->string('summary', 100);
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('sort')->default(0);
            $table->integer('province');
            $table->integer('city');
            $table->integer('area');
            $table->integer('design_company_id');
            $table->integer('user_id')->default(0);
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
        Schema::dropIfExists('customers');
    }
}
