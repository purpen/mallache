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

        // 子账户上线 主账户角色数据变更
        Commands\ChangeCompanyRole::class,

        // 批量更新奖项案例: 封面图、简述、内容、状态等
        Commands\UpdateAwardCase::class,

        // 批量更新案例作品
        Commands\UpdateDesignCase::class,

        //更改子账户数量
        Commands\UpdateChildCount::class,

        // 同步需求项目名称到item表
        Commands\UpdateItemName::class,
        // 同步项目中产品UI老数据
        Commands\UpdateItemUProductDesign::class,



        //更新项目下任务总数量，完成未完成数量的统计
        Commands\UpdateTaskCount::class,

        //更新报价合同记录表的状态
        Commands\ChangeNotification::class,

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

        // 每五分钟检测一次 通知报价合同
        $schedule->command('change:notification')->everyFiveMinutes();

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
