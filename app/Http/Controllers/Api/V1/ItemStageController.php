<?php

namespace App\Http\Controllers\Api\V1;

use App\Helper\Tools;
use App\Http\Controllers\Api\Admin\BaseController;
use App\Http\Transformer\ItemStageTransformer;
use App\Jobs\SendOneSms;
use App\Models\AssetModel;
use App\Models\DesignCompanyModel;
use App\Models\FundLog;
use App\Models\Item;
use App\Models\ItemStage;
use App\Models\User;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class ItemStageController extends BaseController
{
    /**
     * @api {get} /itemStage/designCompany/lists 设计公司项目阶段展示
     * @apiVersion 1.0.0
     * @apiName itemStage designLists
     * @apiGroup itemStage
     *
     * @apiParam {integer} item_id  项目id
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     * "data": {
     * "id": 2,
     * "item_id": 2,
     * "design_company_id": 49,
     * "title": "哈哈",
     * "summary": "备注",
     * "item_stage_image": [],
     * 'percentage': 0.1, // 百分比
     * 'amount': 100.00,    //金额
     * 'time': 122141111,  //阶段时间
     * 'confirm': 0,       // 项目需求方是否确认 0.未确认；1.已确认
     * "sort": 1 //阶段序号
     * },
     * "meta": {
     * "message": "Success",
     * "status_code": 200
     * }
     * }
     */
    public function designLists(Request $request)
    {
        $item_id = $request->input('item_id');
        $itemStage = ItemStage::where('item_id', $item_id)->get();
        if (!$itemStage) {
            return $this->response->array($this->apiError('not found item_stage', 404));
        }
        return $this->response->collection($itemStage, new ItemStageTransformer())->setMeta($this->apiMeta());

    }

    /**
     * @api {get} /itemStage/demand/lists 需求公司项目阶段展示
     * @apiVersion 1.0.0
     * @apiName itemStage demandLists
     * @apiGroup itemStage
     *
     * @apiParam {integer} item_id  项目id
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     * "data": {
     * "id": 2,
     * "item_id": 2,
     * "design_company_id": 49,
     * "title": "哈哈",
     * "summary": "备注",
     * "item_stage_image": [],
     * 'percentage': 0.1, // 百分比
     * 'amount': 100.00,    //金额
     * 'time': 122141111,  //阶段时间
     * 'confirm': 0,       // 项目需求方是否确认 0.未确认；1.已确认
     * "sort": 1 //阶段序号
     * },
     * "meta": {
     * "message": "Success",
     * "status_code": 200
     * }
     * }
     */
    public function demandLists(Request $request)
    {
        $item_id = $request->input('item_id');
        $itemStage = ItemStage::where('item_id', $item_id)->where('status', 1)->get();
        if (!$itemStage) {
            return $this->response->array($this->apiError('not found item_stage', 404));
        }
        return $this->response->collection($itemStage, new ItemStageTransformer())->setMeta($this->apiMeta());

    }


    /**
     * @api {get} /itemStage/{itemStage_id} 根据项目阶段id查看详情
     * @apiVersion 1.0.0
     * @apiName itemStage show
     * @apiGroup itemStage
     *
     * @apiParam {string} token
     */
    public function show($itemStage_id)
    {
        $itemStage_id = intval($itemStage_id);
        $itemStage = ItemStage::find($itemStage_id);
        if (!$itemStage) {
            return $this->response->array($this->apiError('not found', 404));
        }
        return $this->response->item($itemStage, new ItemStageTransformer())->setMeta($this->apiMeta());
    }


    /**
     * @api {put} /itemStage/ok/status 确认发布
     * @apiVersion 1.0.0
     * @apiName itemStage okStatus
     * @apiGroup itemStage
     *
     * @apiParam {integer} id
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *  "meta": {
     *    "code": 200,
     *    "message": "Success.",
     *  }
     * }
     */
    public function okStatus(Request $request)
    {
        $id = $request->input('id');
        $itemStage = ItemStage::where('id', $id)->first();
        if (!$itemStage) {
            return $this->response->array($this->apiError('not found itemStage', 404));
        }
        $status = ItemStage::status($id, 1);
        if (!$status) {
            return $this->response->array($this->apiError('修改失败', 500));
        }

        //项目信息
        $item = Item::find($itemStage->item_id);
        $item_info = $item->itemInfo();
        //通知需求公司
        $title = '项目阶段';
        $content = '设计公司已提交【' . $item_info['name'] . '】项目阶段内容,请前往确认';
        $tools = new Tools();
        $tools->message($item->user_id, $title, $content, 2, $itemStage->item_id);

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {put} /itemStage/un/status 项目关闭发布
     * @apiVersion 1.0.0
     * @apiName itemStage unStatus
     * @apiGroup itemStage
     *
     * @apiParam {integer} id
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *  "meta": {
     *    "code": 200,
     *    "message": "Success.",
     *  }
     * }
     */
    public function unStatus(Request $request)
    {
        $id = $request->input('id');
        $itemStage = ItemStage::where('id', $id)->first();
        if (!$itemStage) {
            return $this->response->array($this->apiError('not found itemStage', 404));
        }
        $status = ItemStage::status($id, 0);
        if (!$status) {
            return $this->response->array($this->apiError('修改失败', 500));
        }
        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {post} /itemStage/demandFirmItemStage 发布需求方确认项目阶段
     * @apiVersion 1.0.0
     * @apiName itemStage demandFirmItemStage
     * @apiGroup itemStage
     *
     * @apiParam {integer} item_stage_id  阶段ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *  "meta": {
     *    "code": 200,
     *    "message": "Success.",
     *  }
     * }
     */
    public function demandFirmItemStage(Request $request)
    {
        $item_stage_id = $request->input('item_stage_id');

        if (!$item_stage = ItemStage::find($item_stage_id)) {
            return $this->response->array($this->apiError('not found itemStage', 404));
        }

        // 判断用户有无操作权限
        if (Item::where(['id' => $item_stage->item_id, 'user_id' => $this->auth_user_id])->count() < 1) {
            return $this->response->array($this->apiError('无操作权限', 403));
        }

        //判断阶段是否按sort 顺序确认；
        $next_item_stage = ItemStage::where(['item_id' => $item_stage->item_id, 'confirm' => 0])->orderBy('sort', 'asc')->first();
        //如不存在 说明阶段都已确认 返回成功
        if (!$next_item_stage) {
            return $this->response->array($this->apiSuccess());
        }
        if ($next_item_stage->sort != $item_stage->sort || $item_stage->confir == 1) {
            return $this->response->array($this->apiError('当前阶段不可操作', 403));
        }

        try {
            DB::beginTransaction();

            // 项目阶段确认
            $item_stage->confirm = 1;
            $item_stage->save();

            //项目信息
            $item = Item::find($item_stage->item_id);
            $item_info = $item->itemInfo();
            // 设计公司用户信息
            $design_user = $item->designCompany->user;

            $tools = new Tools();
            //通知设计公司
            $title2 = '需求公司确认阶段';
            $content2 = '【' . $item_info['name'] . '】项目阶段已确认';
            $tools->message($design_user->id, $title2, $content2, 2, $item_stage->item_id);
            // 短信通知设计公司
            $this->sendSms($item->designCompany->phone);

            // 如果合同版本为 0 ，直接确认需求公司阶段款已付
            $contract = $item->contract;
            if ($contract && $contract->version == 0) {
                // 确认需求公司阶段款已付
                $item_stage->pay_status = 1;
                $item_stage->save();

                // 执行--- 阶段确认、钱包转账、资金流水记录、消息通知
                if (!$this->pay($item->user_id, $design_user->id, $item_stage->amount, $item, $item_stage)) {
                    throw new \Exception('支付错误', 500);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            $this->response->array($this->apiError($e->getMessage(), $e->getCode()));
        }

        return $this->response->array($this->apiSuccess());
    }


    /**
     * 阶段付款 （一次性托管版本：0）
     * @param int $demand_user_id 需求用户ID
     * @param int $design_user_id 设计公司ID
     * @param float $amount 支付金额
     * @param object $item 项目实例
     * @param object $item_stage 项目阶段实例
     * @return bool
     */
    protected function pay($demand_user_id, $design_user_id, $amount, $item, $item_stage)
    {
        // 项目阶段确认
        $item_stage->confirm = 1;
        $item_stage->save();

        $user_model = new User();
        //修改项目剩余项目款
        $item->rest_fund = $item->rest_fund - $amount;
        $item->save();

        //减少需求公司账户金额（总金额、冻结金额）
        $user_model->totalAndFrozenDecrease($demand_user_id, $amount);

        //增加设计公司账户总金额
        $user_model->totalIncrease($design_user_id, $amount);

        $item_info = $item->itemInfo();

        $fund_log = new FundLog();
        //需求公司资金流水记录
        $fund_log->outFund($demand_user_id, $amount, 1, $design_user_id, '支付【' . $item_info['name'] . '】项目阶段项目款');
        //设计公司资金流水记录
        $fund_log->inFund($design_user_id, $amount, 1, $demand_user_id, '收到【' . $item_info['name'] . '】项目阶段项目款');


        $tools = new Tools();
        //通知需求公司
        $title1 = '支付阶段项目款';
        $content1 = '已支付【' . $item_info['name'] . '】项目阶段项目款';
        $tools->message($demand_user_id, $title1, $content1, 3, null);
        // 短信通知需求公司
        $this->sendSms($item->phone);

        //通知设计公司
        $title2 = '收到阶段项目款';
        $content2 = '已收到【' . $item_info['name'] . '】项目阶段项目款';
        $tools->message($design_user_id, $title2, $content2, 3, null);
        // 短信通知设计公司
        $this->sendSms($item->designCompany->phone);

        return true;
    }

    public function sendSms($phone)
    {
        $text = config('constant.sms_fix') . '您好，您在铟果平台的项目最新状态已更新，请您及时登录查看，并进行相应操作。感谢您的信任，如有疑问欢迎致电 ' . config('constant.notice_phone') . '。';
        dispatch(new SendOneSms($phone, $text));
    }

}
