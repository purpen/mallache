<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRecommendToItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*recommend	varchar(100)	是		推荐设计公司
ord_recommend	varchar(100)	是		已推荐设计公司*/
        Schema::table('item', function (Blueprint $table) {
            $table->string('recommend',100)->default('');
            $table->string('ord_recommend', 100)->default('');
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
            $table->dropColumn(['ord_recommend', 'recommend']);
        });
    }
}
