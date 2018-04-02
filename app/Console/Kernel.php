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


        // 注销账户
        Commands\UnsetUser::class,

        // 项目类型多选数据结构变更
        Commands\ChangeItem::class,

        // 每日定时更新内容的随机数，用于内容随机排序
        Commands\UpdateRandom::class,

        // 删除过期的云盘分享
        Commands\ClearPanShare::class,

        // 清除设计云盘回收站过期的文件
        Commands\ClearRecycleBin::class,


    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('Weighted:calculation')->everyFiveMinutes();

        $schedule->command('payOrder:drop')->everyFiveMinutes();

        $schedule->command('Update:token')->everyFiveMinutes();
        // 每日定时更新内容的随机数，用于内容随机排序
        $schedule->command('random:update')->daily();

        // 每小时 运行一次删除过期云盘分享任务
        $schedule->command('clear:panShare')->hourly();

        // 每天凌晨3点 清除设计云盘回收站过期的文件
        $schedule->command('clear:RecycleBin')->dailyAt('3:00');


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
