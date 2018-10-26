<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\AssetModel;
use App\Models\DesignResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Dingo\Api\Exception\StoreResourceFailedException;
use App\Http\Transformer\DesignResultListTransformer;

class AdminDesignResultController extends BaseController
{
    /**
     * @api {post} /admin/designResult/show 设计成果详情
     * @author 王松
     * @apiVersion 1.0.0
     * @apiName designResultsShow
     * @apiGroup designResults
     * @apiParam {string} token
     * @apiParam {string} id
     * @apiSuccessExample 成功响应:
     * {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      },
     *      "data": {
     *          "title": "标题", //标题
     *          "content": "内容", //描述
     *          "cover_id": 999, //封面图id
     *          "sell_type": "1", //售卖类型 1:全款,2:股权合作
     *          "price": "100000", //售卖价格
     *          "status": "1", //状态 1:待提交,2:审核中,3:已上架,-1:已下架
     *          "share_ratio": "100", //股权比例
     *          "design_company_id": "66", //设计公司ID
     *          "user_id": 11, //用户id
     *          "thn_cost": 10, //平台佣金比例
     *          "follow_count": 0, //关注数量
     *          "demand_company_id": 0, //购买需求公司ID
     *          "purchase_user_id": 0, //购买用户ID
     *          "updated_at": 1540433203,
     *          "created_at": 1540433203, //创建时间
     *          "id": 1 //设计成果ID
     *      }
     * }
     */
    public function designResultsShow(Request $request)
    {
        $all = $request->all();
        $rules = [
            'id' => 'required|integer'
        ];
        $validator = Validator::make($all, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException(403,$validator->errors());
        }
        $design_result = DesignResult::find($all['id']);
        if(!empty($design_result)){
            $cover_url = AssetModel::find($design_result->cover_id);
            $images_url = AssetModel::getImageUrl($design_result->id,37,2);
            $illustrate_url = AssetModel::getImageUrl($design_result->id,38,2);
            if($cover_url){
                $design_result->cover = $cover_url;
            }else{
                $design_result->cover = '';
            }
            $design_result->images_url = $images_url;
            $design_result->illustrate_url = $illustrate_url;
        }
        return $this->apiSuccess('Success', 200,$design_result ?? []);
    }

    /**
     * @api {get} /admin/designResult/list 设计成果待审核列表
     * @author 王松
     * @apiVersion 1.0.0
     * @apiName designResultsUnauditedLists
     * @apiGroup designResults
     * @apiParam {integer} page 页数
     * @apiParam {integer} per_page 页面条数
     * @apiParam {integer} sort 0:升序,1:降序(默认)
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     * "data": [
     *     {
     *         "id": 1,
     *         "title": "标题", //标题
     *         "content": "内容", //描述
     *         "cover_id": 999,
     *         "sell_type": 1, //售卖类型 1.全款 2.股权合作
     *         "price": "100000.00", //售价
     *         "share_ratio": 100, //股权比例
     *         "design_company_id": 66, //设计公司ID
     *         "user_id": 87,
     *         "cover": { //封面
     *             "id": 999,
     *             "name": "participants@2x.png",
     *             "created_at": 1524207783,
     *             "summary": null,
     *             "size": 939,
     *             "file": "https://d3g.taihuoniao.com/saas/20180420/5ad990a7daf30",
     *             "small": "https://d3g.taihuoniao.com/saas/20180420/5ad990a7daf30-p280x210.jpg",
     *             "big": "https://d3g.taihuoniao.com/saas/20180420/5ad990a7daf30-p800.jpg",
     *             "logo": "https://d3g.taihuoniao.com/saas/20180420/5ad990a7daf30-p180x180.jpg",
     *             "middle": "https://d3g.taihuoniao.com/saas/20180420/5ad990a7daf30-p450x255"
     *          },
     *          "status": 1, //状态 1. 待提交，2.审核中；3.已上架;-1.已下架
     *          "thn_cost": "10.00", //平台佣金比例
     *          "follow_count": 0, //关注数量
     *          "demand_company_id": 0, //购买需求公司ID
     *          "purchase_user_id": 0, //购买用户ID
     *          "created_at": 1540448935, //创建时间
     *          "updated_at": 1540448935
     *     }
     * ],
     * "meta": {
     *     "message": "Success",
     *     "status_code": 200,
     *     "pagination": {
     *         "total": 1,
     *         "count": 1,
     *         "per_page": 10,
     *         "current_page": 1,
     *         "total_pages": 1,
     *         "links": []
     *      }
     *  }
     * }
     */
    public function unauditedLists(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        if ($request->input('sort') == 0 && $request->input('sort') !== null) {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        //收藏的项目成果
        $query = DesignResult::query();
        $list = $query->where('status',2)->orderBy('id',$sort)->paginate($per_page);
        return $this->response->paginator($list, new DesignResultListTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /admin/designResult/save 设计成果审核
     * @author 王松
     * @apiVersion 1.0.0
     * @apiName designResultsToExamines
     * @apiGroup designResults
     * @apiParam {integer} id 设计成果ID
     * @apiParam {integer} type 审核类型 1:通过,2:驳回
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     * "meta": {
     *     "message": "保存状态成功",
     *     "status_code": 200
     *  }
     * }
     */
    public function toExamine(Request $request)
    {
        $all = $request->all();
        $rules = [
            'id' => 'required|integer',
            'type' => 'required|integer'
        ];
        $validator = Validator::make($all, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException(403,$validator->errors());
        }
        $design_result = DesignResult::where('id',$all['id'])->where('status','>',0)->first();
        if(!$design_result){
            return $this->apiError('设计成果不存在', 400);
        }
        if( $design_result->status != 2){
            return $this->apiError('不是待审核状态', 400);
        }
        if($all['type'] == 1){
            $design_result->status = 3;
            $msg = '审核通过';
        }else{
            $design_result->status = 1;
            $msg = '审核驳回';
        }
        if($design_result->save()){
            return $this->apiSuccess($msg, 200);
        }
        return $this->apiError('审核失败', 400);
    }

}
