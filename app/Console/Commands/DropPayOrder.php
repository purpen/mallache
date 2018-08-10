<?php

namespace App\Console\Commands;

use App\Models\PayOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DropPayOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payOrder:drop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '删除过期的支付订单';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 过期时长
        $out_time = 3600 * 24;

        // 过期时间
        $time = date("Y-m-d H:i:s", time() - $out_time);

        $pay_orders = PayOrder::where(['status' => 0])
            ->where('pay_type', '!=', 5)// 忽略银行转账
            ->where('created_at', '<', $time)->get();
        foreach ($pay_orders as $obj) {
            $obj->status = -1;
            $obj->save();
        }

        Log::info('执行清除过期支付订单任务');
        echo "清除完毕";
    }
}
