<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearPanShare extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:panShare';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '删除过期的文件分享';

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
        $time_7 = date("Y-m-d H:i:s", $new_time - (7 * 24 * 3600));
        $time_30 = date("Y-m-d H:i:s", $new_time - (30 * 24 * 3600));
        DB::delete('delete from pan_share where (created_at < ? and share_time = 7) or (created_at < ? and share_time = 30)', [$time_7, $time_30]);

        $this->info('删除过期云盘完成');
    }
}
