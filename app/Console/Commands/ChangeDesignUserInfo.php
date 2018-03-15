<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ChangeDesignUserInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'designUser:change';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '修改设计公司用户数据修改';

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
        User::chunk(100, function ($users) {
            foreach ($users as $user) {
                $user->child_account = 1;
                $user->company_role = 20;
                $user->save();
            }
        });

        $this->info('结构数据调整完毕');
    }
}
