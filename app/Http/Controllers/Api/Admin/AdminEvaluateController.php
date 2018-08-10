<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Item;
use App\Models\Evaluate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Support\Facades\DB;
use App\Events\ItemStatusEvent;
use App\Http\Transformer\EvaluateTransformer;
class AdminEvaluateController extends Controller
{
    /**
     * @api {post} /admin/platform/evaluate 平台评价项目
     * @apiVersion 1.0.0
     * @apiName platform evaluate
     * @apiGroup platformEvaluate
     * @apiParam {string} token
     * @apiParam {integer} item_id 项目id
     * @apiParam {integer} design_progress 设计进度(平台评价时必传)
     * @apiParam {integer} results 成果质量(平台评价时必传)
     * @apiParam {integer} project_quotation 项目报价(平台评价时必传)
     * @apiParam {integer} contract 合同签约(平台评价时必传)
     * @apiParam {integer} communicate 沟通态度(平台评价时必传)
     * @apiParam {integer} efficiency 解决效率(平台评价时必传)
     * @apiSuccessExample 成功响应:
     * {
     *     "meta": {
     *         "message": "Success",
     *         "status_code": 200
     *     }
     * }
     */
    public function platformEvaluate(Request $request)
    {
        $rules = [
            'item_id' => 'required|integer',
            'design_progress' => 'required|integer|max:10',
            'results' => 'required|integer|max:10',
            'project_quotation' => 'required|integer|max:10',
            'contract' => 'required|integer|max:10',
            'communicate' => 'required|integer|max:10',
            'efficiency' => 'required|integer|max:10',
        ];
        $params = $request->only(['item_id','design_progress','results','project_quotation','contract','communicate','efficiency']);
        $validator = Validator::make($params, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        if (!$item = Item::find($params['item_id'])) {
            return $this->response->array($this->apiError('not found item', 404));
        }
        $params['demand_company_id'] = $this->auth_user->demand_company_id;
        $params['design_company_id'] = $item->design_company_id;
        $evaluate = new Evaluate;
        $res = $evaluate->get_one($params['item_id']);
        $score = 0;
        $score += $params['results'] * config('evaluate.results'); //成果质量比重
        $score += $params['design_progress'] * config('evaluate.design_progress'); //设计进度比重
        $score += $params['contract'] * config('evaluate.contract'); //合同签约比重
        $score += $params['project_quotation'] * config('evaluate.project_quotation'); //项目报价比重
        $score += $params['efficiency']  * config('evaluate.efficiency'); //解决效率比重
        $score += $params['communicate']  * config('evaluate.communicate'); //沟通态度比重
        $platform_score['results'] = (int)$params['results'];
        $platform_score['design_progress'] = (int)$params['design_progress'];
        $platform_score['contract'] = (int)$params['contract'];
        $platform_score['project_quotation'] = (int)$params['project_quotation'];
        $platform_score['efficiency'] = (int)$params['efficiency'];
        $platform_score['communicate'] = (int)$params['communicate'];
        $list['platform_score'] = json_encode($platform_score);
        if(!empty($res)){ //评价存在更新
            if(!empty($res->platform_score)){
                return $this->response->array($this->apiError('已经评价过了', 200));
            }
            //计算评分
            $score = $score * 0.5;
            //把平台评分减去百分之五十,再加上用户评分,算出总评分
            $score += $res->score * 0.5;
            $score = sprintf('%.1f',$score);
            try {
                DB::beginTransaction();
                if(empty($res->demand_company_id)){
                    $res->demand_company_id = $params['demand_company_id'];
                }
                if(empty($res->design_company_id)){
                    $res->design_company_id = $params['design_company_id'];
                }
                $res->score = $score;
                $res->platform_score = $list['platform_score'];
                $res->save();
                $item->status = 22;
                $item->save();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e);
                return $this->response->array($this->apiError('database error', 500));
            }
            return $this->response->item($evaluate, new EvaluateTransformer)->setMeta($this->apiMeta());
        }else{ //评价不存在新增
            if($score > 10){
                $score = 10;
            }
            if($score < 0){
                $score = 0;
            }
            $score = sprintf('%.1f',$score);
            $list['content'] = '';
            $list['user_score'] = '';
            $list['item_id'] = (int)$params['item_id'];
            $list['score'] = $score;
            $list['demand_company_id'] = $params['demand_company_id'];
            $list['design_company_id'] = $params['design_company_id'];
            try {
                DB::beginTransaction();
                Evaluate::create($list);
                $item->status = 22;
                $item->save();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e);
                return $this->response->array($this->apiError('database error', 500));
            }
            return $this->response->item($evaluate, new EvaluateTransformer)->setMeta($this->apiMeta());
        }
    }
}
