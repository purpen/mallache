<?php

namespace App\Console\Commands;

use App\Models\RecycleBin;
use Illuminate\Console\Command;

class ClearRecycleBin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:RecycleBin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '清除设计云盘回收站过期的文件';

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
        $new_time = time();
        $time_30 = date("Y-m-d H:i:s", $new_time - (30 * 24 * 3600));

        $recycle_bin_arr = RecycleBin::select('id')
            ->where('created_at', '<', $time_30)
            ->get()->pluck('id')->all();

        $recycle_bin_arr = array_chunk($recycle_bin_arr, 100);
        foreach ($recycle_bin_arr as $recycle_bin) {
            $RecycleBins = RecycleBin::whereIn('id', $recycle_bin)->get();
            foreach ($RecycleBins as $RecycleBin) {
                $RecycleBin->deleteRecycle();
            }
        }

        $this->info('设计云盘回收站过期的文件清除完毕');
    }
}
