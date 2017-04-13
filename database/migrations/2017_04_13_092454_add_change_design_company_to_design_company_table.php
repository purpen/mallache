<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChangeDesignCompanyToDesignCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('design_company', function (Blueprint $table) {
            $table->string('company_name' ,  50)->change();
            $table->string('registration_number' ,15)->change();
            $table->integer('province')->change();
            $table->integer('city')->change();
            $table->integer('area')->change();
            $table->string('address' , 50)->change();
            $table->string('contact_name' , 20)->change();
            $table->string('position' , 20)->change();
            $table->string('phone' , 20)->change();
            $table->string('email' , 50)->change();
            $table->string('design_type' ,  50)->nullable()->change();
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
            $table->dropColumn([
                'company_name',
                'registration_number',
                'province',
                'city',
                'area',
                'address',
                'contact_name',
                'position',
                'phone',
                'email',
                'design_type'
            ]);
        });
    }
}
