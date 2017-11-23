<?php

namespace App\Console\Commands;

use App\Models\DesignCompanyModel;
use App\Models\User;
use Illuminate\Console\Command;

class createUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create {--count=10 :创建数量（默认10）}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建用户命令: --count 创建数量';

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
        $count = $this->option('count');

        $arr = [];

        for($i = 0; $i < $count; $i++){
            $phone = '188' . random_int(10000000, 99999999);
            $arr[] = $phone;

            $user = User::create([
                'account' => $phone,
                'phone' => $phone,
                'username' => $phone,
                'type' => 2,
                'status' => 1,
                'password' => bcrypt('123456'),
                'auto_create' => 1,
            ]);
            DesignCompanyModel::createDesign($user->id);
        }

        $this->info('生成账号:' . implode(',',$arr));

    }
}
