<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UnsetUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'unset:user {--account=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '注销用户 --account= 用户账户';

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
        $account = $this->option('account');
        if(!$account){
         $this->error('未写入需注销账户');
         return;
        }
        $user = User::where('account',$account)->first();
        if(!$user){
            $this->error('输入的账户不存在');
            return;
        }

        if ($this->confirm('确认删除账户：' . $account . '?')) {
            $user->unsetUser();
            $this->info('注销成功');
        }else{
            $this->info('取消注册');
        }
    }
}
