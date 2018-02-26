<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //计算设计公司权重值命令
        Commands\CalculationWeight::class,
        //删除过期的支付订单
        Commands\DropPayOrder::class,

        // 批量生成设计公司用户
        Commands\CreateUsers::class,

        //更改veer的token
        Commands\UpdateVeerToken::class,

        //excel导出
        Commands\designCompanyExcel::class,
        //设计公司案例数据结构变更
        Commands\ChangeDesignCase::class,

        // 项目类型多选数据结构变更
        Commands\ChangeItem::class

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('Weighted:calculation')->everyFiveMinutes();

        $schedule->command('payOrder:drop')->everyFiveMinutes();

        $schedule->command('Update:token')->everyFiveMinutes();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
