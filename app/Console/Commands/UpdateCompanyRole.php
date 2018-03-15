<?php

namespace App\Console\Commands;

use App\Models\DesignCompanyModel;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateCompanyRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Update:companyRole';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新设计公司为超级管理员';

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
        $design_company_users = User::where('design_company_id', '!=' , 0)->get();
        foreach ($design_company_users as $design_company_user){
            $design_company_user->child_account = 1;
            $design_company_user->company_role = 20;
            $design_company_user->save();
        }
        $this->info('更改完设计公司用户的管理员身份和主账户');
    }
}
