<?php

namespace App\Console\Commands;

use App\Helper\Tools;
use App\Models\DesignCompanyModel;
use App\Models\Item;
use App\Models\ItemRecommend;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ChangeNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更改报价合同记录表状态';

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
        //报价
        $q_notifications = Notification::where('status' , 0)->where('type' , 1)->get();
        //获取所有相关推荐表的id
        $q_item_recommend_ids = [];
        foreach ($q_notifications as $q_notification){
            if($q_notification->count == 3){
                $q_notification->status = 1;
                $q_notification->save();
            } else if(time() > $q_notification->inform_time && $q_notification->count < 3) {
                $q_notification->count += 1;
                $q_notification->inform_time = $q_notification->inform_time + config('constant.inform_time');
                $q_notification->save();
            }
            $q_item_recommend_ids[] = $q_notification->target_id;
        }
        $q_new_item_recommend_ids = $q_item_recommend_ids;
        //获取所有符合条件的相关推荐的设计公司
        $q_item_recommends = ItemRecommend::whereIn('id' , $q_new_item_recommend_ids)->where('item_status' , 0)->where('design_company_status' , 0)->get();

        //获取所有设计设计公司
        foreach ($q_item_recommends as $q_item_recommend){
            $q_design_company = DesignCompanyModel::where('id' , $q_item_recommend->design_company_id)->first();
            if($q_design_company){
                $q_content = '需求方已选择贵公司，请您尽快报价';
                Log::info($q_design_company->phone);
                Tools::sendSmsToPhone($q_design_company->phone , $q_content);
            }
        }
        $this->info('报价通知完毕');


        //合同
        $c_notifications = Notification::where('status' , 0)->where('type' , 2)->get();
        //获取所有相关推荐表的id
        $c_item_recommend_ids = [];
        foreach ($c_notifications as $c_notification){
            if($c_notification->count == 3){
                $c_notification->status = 1;
                $c_notification->save();
            } else if(time() > $c_notification->inform_time && $c_notification->count < 3) {
                $c_notification->count += 1;
                $c_notification->inform_time = $c_notification->inform_time + config('constant.inform_time');
                $c_notification->save();
            }
            $c_item_recommend_ids[] = $c_notification->target_id;
        }
        $c_new_item_recommend_ids = $c_item_recommend_ids;
        //获取所有符合条件的相关推荐的设计公司
        $c_item_recommends = Item::whereIn('id' , $c_new_item_recommend_ids)->where('status' , '<', 6)->get();

        //获取所有设计设计公司
        foreach ($c_item_recommends as $c_item_recommend){
            $c_design_company = DesignCompanyModel::where('id' , $c_item_recommend->design_company_id)->first();
            if($c_design_company){
                $c_content = '需求方已选择贵公司，请您尽快填写合同';
                Log::info($c_design_company->phone);
                Tools::sendSmsToPhone($c_design_company->phone , $c_content);
            }
        }
        $this->info('合同通知完毕');
    }
}
