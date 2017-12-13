<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsTodateOfAwardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('date_of_awards', function (Blueprint $table) {
            $table->integer('user_id');
            $table->integer('cover_id')->default(0);
            $table->text('content');
            $table->string('url', 100);
            $table->tinyInteger('kind')->default(1);
            $table->tinyInteger('evt')->default(0);
            $table->tinyInteger('status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('date_of_awards', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'content', 'cover_id', 'url', 'evt', 'kind', 'status']);
        });
    }
}
