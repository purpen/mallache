<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ChangeCompanyRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change:companyRole';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '子账户上线 主账户角色数据变更';

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
        User::where(['type' => 2, 'child_account' => 0])->chunk(100, function ($users) {
            foreach ($users as $user) {
                $user->company_role = 20;
                $user->save();
            }
        });

        $this->info('子账户上线 主账户角色数据变更完成');
    }
}
