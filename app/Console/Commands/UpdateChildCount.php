<?php

namespace App\Console\Commands;

use App\Models\DesignCompanyModel;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateChildCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'childCount:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更改子账户数量';

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
        $DesignCompanies = DesignCompanyModel::get();
        foreach ($DesignCompanies as $DesignCompany){
            //子账户数量
            $child_user = User::where('design_company_id' , $DesignCompany->id)->where('child_account' , 1)->count();
            if($child_user !== 0){
                //父账户
                $p_user = User::where('design_company_id' , $DesignCompany->id)->where('child_account' , 0)->first();
                $p_user->child_count = intval($child_user);
                $p_user->save();
            }

        }
    }
}
