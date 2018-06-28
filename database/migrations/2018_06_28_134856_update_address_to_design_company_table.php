<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddressToDesignCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::getConnection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('json', 'string');

        Schema::table('design_company', function (Blueprint $table) {
            $table->string('address', 100)->nullable()->change();
        });
        Schema::table('demand_company', function (Blueprint $table) {
            $table->string('address', 100)->nullable()->change();
        });
        Schema::table('item', function (Blueprint $table) {
            $table->string('address', 100)->nullable()->change();
        });
        Schema::table('contract', function (Blueprint $table) {
            $table->string('design_company_address', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('design_company', function (Blueprint $table) {
            $table->string('address' , 50)->nullable();
        });
        Schema::table('demand_company', function (Blueprint $table) {
            $table->string('address' , 50)->nullable();
        });
        Schema::table('item', function (Blueprint $table) {
            $table->string('address' , 50)->nullable();
        });
        Schema::table('contract', function (Blueprint $table) {
            $table->string('design_company_address' , 50)->nullable();
        });
    }
}
