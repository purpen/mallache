<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeInfoToDesignCase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('design_case', function (Blueprint $table) {
            /*patent	json	是		获得专利 1.发明专利；2.实用新型专利；3.外观设计专利 [{'time':2018-1-1,'type': 1}]
            prizes	json	是		奖项:1.德国红点设计奖;2.德国IF设计奖;3.IDEA工业设计奖;4.中国红星奖;5.中国红棉奖;6.台湾金点奖;7.香港DFA设计奖 ;8.日本G-Mark设计奖;9.韩国好设计奖;10.新加坡设计奖;11.意大利—Compasso dOro设计奖;12.英国设计奖;13.中国优秀工业设计奖；14.DIA中国设计智造大奖；15.中国好设计奖；16.澳大利亚国际设计奖;20:其他 [{'time':2018-1-1,'type': 1}]*/

            $table->json('patent');
            $table->json('prizes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('design_case', function (Blueprint $table) {
            $table->dropColumn(['patent', 'prizes']);
        });
    }
}
