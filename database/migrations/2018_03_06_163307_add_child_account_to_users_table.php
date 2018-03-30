<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChildAccountToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('invite_company_id')->default(0);
            $table->tinyInteger('child_account')->default(0);
            $table->tinyInteger('company_role')->default(0);
            $table->tinyInteger('department')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'Invite_company_id',
                'child_account',
                'company_role',
                'department'
            ]);
        });
    }
}
