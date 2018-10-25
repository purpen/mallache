<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Follow;
use App\Models\AssetModel;
use App\Models\DesignResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Dingo\Api\Exception\StoreResourceFailedException;
use App\Http\Transformer\DesignResultListTransformer;

class DesignResultController extends BaseController
{
    /**
     * @api {post} /designResults/add 保存设计成果
     * @author 王松
     * @apiVersion 1.0.0
     * @apiName addDesignResults
     * @apiGroup designResults
     * @apiParam {string} token
     * @apiParam {string} title 标题
     * @apiParam {string} content 描述
     * @apiParam {array} images 图片
     * @apiParam {array} illustrate 说明书
     * @apiParam {integer} sell_type 售卖类型  1:全款,2:股权合作
     * @apiParam {string} price 售价
     * @apiParam {integer} share_ratio 股权比例
     * @apiParam {integer} design_company_id 设计公司ID
     * @apiParam {integer} status 状态 1:待提交,2:审核中,3:已上架,-1:已下架
     *
     * @apiSuccessExample 成功响应:
     * {
     *      "meta": {
     *          "message": "保存成功",
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
    public function addDesignResults(Request $request)
    {
        $all = $request->all();
        $rules = [
            'title' => 'required',
            'content' => 'required',
            'images' => 'required|array',
            'illustrate' => 'array',
            'sell_type' => 'required|integer',
            'price' => 'required',
            'share_ratio' => 'required|integer',
            'design_company_id' => 'required|integer',
            'status' => 'required|integer'
        ];
        $validator = Validator::make($all, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException(403,$validator->errors());
        }
        $user_id = $this->auth_user_id;
        $images_random = $all['images'];
        $illustrate_random = $all['illustrate'];
        //设计成果图片
        $images = AssetModel::select('id','target_id','user_id','name','random','path')->whereIn('random',$images_random)->where('type',37)->get();

        if($images->isEmpty()){
            return $this->apiError('图片信息不存在',403);
        }else{
            $images = $images->toArray();
        }
        $design_result = new DesignResult;
        $design_result->title = $all['title']; //标题
        $design_result->content = $all['content']; //描述
        $design_result->cover_id = $images[0]['id']; //封面图
        $design_result->sell_type = $all['sell_type']; //售卖类型
        $design_result->price = $all['price']; //售价
        $design_result->status = $all['status']; //状态 1.待提交，2.审核中；3.已上架;-1.已下架
        $design_result->share_ratio = $all['share_ratio']; //股权比例
        $design_result->design_company_id = $all['design_company_id']; //设计公司ID
        $design_result->user_id = $user_id; //用户ID
        $design_result->thn_cost = config('commission.rate'); //平台佣金比例
        $design_result->follow_count = 0; //关注数量
        $design_result->demand_company_id = 0; //购买需求公司ID
        $design_result->purchase_user_id = 0; //购买用户ID
        DB::beginTransaction();
        $res = $design_result->save();
        $images_arr = array_column($images,'id');
        //产品说明书
        $illustrate = AssetModel::select('id','target_id','user_id','name','random','path')
            ->whereIn('random',$illustrate_random)
            ->where('type',38)->get();
        if($illustrate){
            $illustrate_arr = array_column($illustrate->toArray(),'id');
            $arr = array_merge($images_arr,$illustrate_arr);
        }else{
            $arr = $images_arr;
        }
        $cover_url = AssetModel::find($design_result->cover_id);
        if($cover_url){
            $design_result->cover_url = $cover_url;
        }else{
            $design_result->cover_url = '';
        }
        $asset = AssetModel::whereIn('id',$arr)->update(['target_id'=>$design_result->id]);
        if(!empty($res) && !empty($asset)){
            DB::commit();
            return $this->apiSuccess('保存成功', 200,$design_result);
        }else{
            DB::rollBack();
            return $this->apiError('保存失败',400);
        }
    }

    /**
     * @api {post} /designResults/show 设计成果详情
     * @author 王松
     * @apiVersion 1.0.0
     * @apiName designResultsShow
     * @apiGroup designResults
     * @apiParam {string} token
     * @apiParam {string} id
     * @apiSuccessExample 成功响应:
     * {
     *      "meta": {
     *          "message": "保存成功",
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
        return $this->apiSuccess('Success', 200,$design_result);
    }

    /**
     * @api {get} /designResults/list 设计成果列表
     * @author 王松
     * @apiVersion 1.0.0
     * @apiName designResultsLists
     * @apiGroup designResults
     * @apiParam {integer} status 状态 0:全部,1:待提交,2:审核中,3:已上架,-1:已下架
     * @apiParam {integer} sort 0:升序,1:降序(默认)
     * @apiParam {integer} page 页数
     * @apiParam {integer} collect 收藏状态：0.默认 1.收藏
     * @apiParam {integer} per_page 页面条数
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
    public function lists(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        $status = $request->input('status') ?? 0;
        $collect = $request->input('collect') ?? 0;
        if ($request->input('sort') == 0 && $request->input('sort') !== null) {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        //收藏的项目成果
        $query = DesignResult::query();
        if ($collect > 0){
            $follow = new Follow;
            if($this->auth_user->type == 1){
                //需求公司
                $follow_data = $follow->getResultFollow($this->auth_user->design_company_id,1);
            }else{
                //设计公司
                $follow_data = $follow->getResultFollow($this->auth_user->demand_company_id,2);
            }
            if($status != 0){
                $query->where('status',$status);
            }
            $list = $query->where('user_id',$this->auth_user_id)
                ->whereIn('id',$follow_data)
                ->orderBy('id',$sort)
                ->paginate($per_page);
        } else {
            if($status != 0){
                $query->where('status',$status);
            }
            $list = $query->where('user_id',$this->auth_user_id)
                ->orderBy('id',$sort)
                ->paginate($per_page);
        }
        return $this->response->paginator($list, new DesignResultListTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /designResults/unauditedLists 待审核设计成果列表
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
        //$status = $request->input('status') ?? 0;
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

}
