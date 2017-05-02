<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDemandToItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item', function (Blueprint $table) {
            $table->string('company_name', 50)->default('');
            $table->string('company_abbreviation', 50)->default('');
            $table->tinyInteger('company_size')->default(0);
            $table->string('company_web',50)->default('');
            $table->integer('company_province')->default(0);
            $table->integer('company_city')->default(0);
            $table->integer('company_area')->default(0);
            $table->string('address', 50)->default('');
            $table->string('contact_name', 20)->default('');
            $table->string('phone',20)->default('');
            $table->string('email', 50)->default('');
            $table->integer('quotation_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item', function (Blueprint $table) {
            $table->dropColumn(['company_name','company_abbreviation', 'company_size', 'company_web', 'company_province', 'company_city', 'company_area', 'address', 'contact_name', 'phone', 'email', 'quotation_id']);
        });
    }
}
