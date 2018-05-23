<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDesignCompanyIdToItemCommission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_commission', function (Blueprint $table) {
            $table->dropColumn('design_user_id');

            $table->integer('design_company_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_commission', function (Blueprint $table) {
            //
        });
    }
}
