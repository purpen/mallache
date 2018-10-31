<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Follow;
use App\Models\AssetModel;
use App\Models\DesignResult;
use App\Models\DesignDemand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Dingo\Api\Exception\StoreResourceFailedException;
use App\Http\Transformer\DesignResultListTransformer;
use App\Http\Transformer\DesignCollectDemandListTransformer;

class DesignResultController extends BaseController
{
    /**
     * @api {post} /designResults/save 保存设计成果
     * @author 王松
     * @apiVersion 1.0.0
     * @apiName saveDesignResults
     * @apiGroup designResults
     * @apiParam {string} token
     * @apiParam {string} id 修改时必传
     * @apiParam {string} title 标题
     * @apiParam {string} content 描述
     * @apiParam {array} images 图片最大20
     * @apiParam {array} patent 专利证书
     * @apiParam {array} illustrate 说明书
     * @apiParam {integer} sell_type 售卖类型  1:全款,2:股权合作
     * @apiParam {string} price 售价
     * @apiParam {integer} share_ratio 股权比例
     * @apiParam {integer} design_company_id 设计公司ID
     * @apiParam {integer} status 状态 1:待提交,2:审核中,3:已上架,-1:已下架
     * @apiParam {integer} contacts 联系人
     * @apiParam {integer} contact_number 联系电话
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
     *          "contacts": "羽落", //联系人
     *          "contact_number": 13217229788, //联系电话
     *          "id": 1, //设计成果ID
     *          "cover": { //封面图
     *             "id": 999,
     *              "name": "participants@2x.png",
     *              "created_at": 1524207783,
     *              "summary": null,
     *              "size": 939,
     *              "file": "https://d3g.taihuoniao.com/saas/20180420/5ad990a7daf30",
     *              "small": "https://d3g.taihuoniao.com/saas/20180420/5ad990a7daf30-p280x210.jpg",
     *              "big": "https://d3g.taihuoniao.com/saas/20180420/5ad990a7daf30-p800.jpg",
     *              "logo": "https://d3g.taihuoniao.com/saas/20180420/5ad990a7daf30-p180x180.jpg",
     *              "middle": "https://d3g.taihuoniao.com/saas/20180420/5ad990a7daf30-p450x255"
     *          }
     *     }
     * }
     */
    public function saveDesignResults(Request $request)
    {
        $all = $request->all();
        $rules = [
            'title' => 'required',
            'content' => 'required',
            'images' => 'required|array|max:20',
            'patent' => 'array',
            'illustrate' => 'array',
            'sell_type' => 'required|integer',
            'price' => 'required',
            'share_ratio' => 'required|integer',
            'design_company_id' => 'required|integer',
            'status' => 'required|integer',
            'contacts' => 'required|integer',
            'contact_number' => 'required|integer|max:11'
        ];
        $validator = Validator::make($all, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException(403,$validator->errors());
        }
        $user_id = $this->auth_user_id;
        $patent = $all['patent'];
        $images_random = $all['images'];
        $illustrate_random = $all['illustrate'];
        //设计成果图片
        $images = AssetModel::select('id','target_id','user_id','name','random','path')->whereIn('random',$images_random)->where('type',37)->get();

        if($images->isEmpty()){
            return $this->apiError('图片信息不存在',403);
        }else{
            $images = $images->toArray();
        }
        //修改
        if(isset($all['id']) && !empty($all['id']) && $all['id'] != 'undefined'){
            $design_result = DesignResult::find((int)$all['id']);
            if(!$design_result){
                return $this->apiError('保存失败',400);
            }
            if($design_result->status < 0){
                return $this->apiError('设计成果已下架',400);
            }
            if($user_id != $design_result->user_id){
                return $this->apiError('没有权限', 400);
            }
            $design_result->status = 2;
        }else{
            $design_result = new DesignResult;
            $design_result->follow_count = 0; //关注数量
            $design_result->demand_company_id = 0; //购买需求公司ID
            $design_result->purchase_user_id = 0; //购买用户ID
            $design_result->thn_cost = config('commission.rate'); //平台佣金比例
            $design_result->user_id = $user_id; //用户ID
            $design_result->status = $all['status'] == 1 ? 1 : 2; //状态 1.待提交，2.审核中；3.已上架;-1.已下架
            $design_result->design_company_id = $all['design_company_id']; //设计公司ID
            $design_result->sell = 0; //0:未出售,1:已出售,2:已确认
        }
        $design_result->title = $all['title']; //标题
        $design_result->content = $all['content']; //描述
        $design_result->cover_id = $images[0]['id']; //封面图
        $design_result->sell_type = $all['sell_type']; //售卖类型 1:全款,2:股权合作
        $design_result->price = $all['price']; //售价
        $design_result->share_ratio = $all['share_ratio']; //股权比例
        $design_result->contacts = $all['contacts']; //联系人
        $design_result->contact_number = $all['contact_number']; //联系电话
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
        //专利证书
        $patent_data = AssetModel::select('id','target_id','user_id','name','random','path')
            ->whereIn('random',$patent)
            ->where('type',39)->get();
        if($patent_data){
            $patent_arr = array_column($patent_data->toArray(),'id');
            $arr = array_merge($arr,$patent_arr);
        }
        $cover_url = AssetModel::find($design_result->cover_id);
        if($cover_url){
            $design_result->cover = $cover_url;
        }else{
            $design_result->cover = '';
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
     * @api {get} /designResults/show 设计成果详情
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
     *          "id": 1, //设计成果ID
     *          "sell":0, //0:未出售,1:已出售,2:已确认
     *          "images_url":[], //图片地址
     *          "illustrate_url":[], //产品说明书
     *          "patent_url":[], //专利证书
     *          "contacts": "羽落", //联系人
     *          "contact_number": 13217229788, //联系电话
     *          "design_company": {}, //设计公司信息
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
        $design_result = DesignResult::where('status','>',0)->where('id',$all['id'])->first();
        if(!empty($design_result)){
            $images_url = AssetModel::getImageUrl($design_result->id,37,2);
            $illustrate_url = AssetModel::getImageUrl($design_result->id,38,2);
            $patent_url = AssetModel::getImageUrl($design_result->id,39,2);
            $design_result->images_url = $images_url;
            $design_result->illustrate_url = $illustrate_url;
            $design_result->patent_url = $patent_url;
            $design_result->design_company = $design_result->designCompany;
            return $this->apiSuccess('Success', 200,$design_result);
        }else{
            return $this->apiError('设计成果已下架',400);
        }
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
     *          "contacts": "羽落", //联系人
     *          "contact_number": 13217229788, //联系电话
     *          "design_company": {}, //设计公司信息
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
     * @api {get} /designResults/saveStatus 设计成果状态修改
     * @author 王松
     * @apiVersion 1.0.0
     * @apiName designResultsSaveStatus
     * @apiGroup designResults
     * @apiParam {integer} id 设计成果ID
     * @apiParam {integer} status 状态 1:待提交,2:审核中,3:已上架,-1:已下架
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
    public function saveStatus(Request $request)
    {
        $all = $request->all();
        $rules = [
            'id' => 'required|integer',
            'status' => 'required|integer'
        ];
        $validator = Validator::make($all, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException(403,$validator->errors());
        }
        $design_result = DesignResult::where('id',$all['id'])->where('status','>',0)->first();
        if(!$design_result){
            return $this->apiError('设计成果不存在', 400);
        }
        if($this->auth_user_id != $design_result->user_id){
            return $this->apiError('保存状态失败', 400);
        }
        if($design_result->sell > 0){
            return $this->apiError('设计成果已出售', 400);
        }
        $design_result->status = $all['status'];
        if($design_result->save()){
            return $this->apiSuccess('保存状态成功', 200);
        }
        return $this->apiError('保存状态失败', 400);
    }

    /**
     * @api {post} /designResults/delete 设计成果删除
     * @author 王松
     * @apiVersion 1.0.0
     * @apiName deleteDesignResult
     * @apiGroup designResults
     * @apiParam {array} id 设计成果ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     * "meta": {
     *     "message": "删除成功",
     *     "status_code": 200
     *  }
     * }
     */
    public function deleteDesignResult(Request $request)
    {
        $all = $request->all();
        $rules = [
            'id' => 'required|array'
        ];
        $validator = Validator::make($all, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException(403,$validator->errors());
        }
        $design_results = DesignResult::whereIn('id',$all['id'])->get();
        if($design_results->isEmpty()){
            return $this->apiError('设计成果不存在', 400);
        }
        foreach ($design_results as $design_result){
            if($this->auth_user_id != $design_result->user_id){
                return $this->apiError('没有权限', 400);
            }
            if(!$design_result->delete()){
                return $this->apiError('删除失败', 400);
            }
        }
        return $this->apiSuccess('删除成功', 200);
    }

    /**
     * @api {get} /designResults/collectionOperation 设计成果收藏与取消收藏
     * @author 王松
     * @apiVersion 1.0.0
     * @apiName DesignResultCollectionOperation
     * @apiGroup designResults
     * @apiParam {integer} id 设计成果ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     * "meta": {
     *     "message": "收藏成功",
     *     "status_code": 200
     *  }
     * }
     */
    public function collectionOperation(Request $request)
    {
        $all = $request->all();
        $rules = [
            'id' => 'required|integer'
        ];
        $validator = Validator::make($all, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException(403,$validator->errors());
        }
        $follow = new Follow;
        $design_result = new DesignResult;
        $id = $all['id'];
        if($this->auth_user->type == 1){
            //需求公司
            $data = $follow->where(['demand_company_id'=>$this->auth_user->demand_company_id,'design_result_id'=>$id,'type'=>2])->first();
            if($data){
                DB::beginTransaction();
                if($data->delete() && $design_result->save_follow_count($id,2)){
                    DB::commit();
                    return $this->apiSuccess('取消收藏成功', 200);
                }
                DB::rollBack();
                return $this->apiError('取消收藏失败', 400);
            }else{
                $follow->demand_company_id = $this->auth_user->demand_company_id;
                $follow->design_result_id = $id;
                $follow->type = 2;
                DB::beginTransaction();
                if($follow->save() && $design_result->save_follow_count($id,1)){
                    DB::commit();
                    return $this->apiSuccess('收藏成功', 200);
                }
                DB::rollBack();
                return $this->apiError('收藏失败', 400);
            }
        }else{
            //设计公司
            $data = $follow->where(['design_company_id'=>$this->auth_user->design_company_id,'design_result_id'=>$id,'type'=>2])->first();
            if($data){
                DB::beginTransaction();
                if($data->delete() && $design_result->save_follow_count($id,2)){
                    DB::commit();
                    return $this->apiSuccess('取消收藏成功', 200);
                }
                DB::rollBack();
                return $this->apiError('取消收藏失败', 400);
            }else{
                $follow->design_company_id = $this->auth_user->design_company_id;
                $follow->design_result_id = $id;
                $follow->type = 2;
                DB::beginTransaction();
                if($follow->save() && $design_result->save_follow_count($id,1)){
                    DB::commit();
                    return $this->apiSuccess('收藏成功', 200);
                }
                DB::rollBack();
                return $this->apiError('收藏失败', 400);
            }
        }
    }

    /**
     * @api {get} /designResults/myCollectionList 我的设计成果收藏列表
     * @author 王松
     * @apiVersion 1.0.0
     * @apiName DesignResultMyCollectionList
     * @apiGroup designResults
     * @apiParam {integer} sort 0:升序,1:降序(默认)
     * @apiParam {integer} type 1:设计需求,2:设计成果
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
     *          "updated_at": 1540448935,
     *          "contacts": "羽落", //联系人
     *          "contact_number": 13217229788, //联系电话
     *          "design_company": {}, //设计公司信息
     *     }
     * ],
     * "meta": {
     *     "message": "Success",
     *     "status_code": 200
     *  }
     * }
     */
    public function myCollectionList(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        if ($request->input('sort') == 0 && $request->input('sort') !== null) {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $all = $request->all();
        $rules = [
            'type' => 'required|integer'
        ];
        $validator = Validator::make($all, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException(403,$validator->errors());
        }
        $type = $all['type'];
        if($type == 1){
            //设计需求
            if ($this->auth_user->type == 1) {
                //需求公司
                $demand_company_id = $this->auth_user->demand_company_id;
                $list = Follow::select('design_demand_id')->where(['type'=>1,'demand_company_id'=>$demand_company_id])->paginate($per_page);
            }else{
                //设计公司
                $design_company_id = $this->auth_user->design_company_id;
                $list = Follow::select('design_demand_id')->where(['type'=>1,'design_company_id'=>$design_company_id])->paginate($per_page);
            }
        }else{
            //设计成果
            if ($this->auth_user->type == 1) {
                //需求公司
                $demand_company_id = $this->auth_user->demand_company_id;
                $list = Follow::select('design_result_id')->where(['type'=>2,'demand_company_id'=>$demand_company_id])->paginate($per_page);
            }else{
                //设计公司
                $design_company_id = $this->auth_user->design_company_id;
                $list = Follow::select('design_result_id')->where(['type'=>2,'design_company_id'=>$design_company_id])->paginate($per_page);
            }
        }
        $list = $list->toArray();
        if($type == 1){
            if(!empty($list['data'])){
                $arr = array_column($list['data'],'design_demand_id');
            }else{
                $arr = [];
            }
            $data = DesignDemand::whereIn('id',$arr)->paginate($per_page);
            return $this->response->paginator($data, new DesignCollectDemandListTransformer)->setMeta($this->apiMeta());
        }else{
            if(!empty($list['data'])){
                $arr = array_column($list['data'],'design_result_id');
            }else{
                $arr = [];
            }
            $data = DesignResult::whereIn('id',$arr)->orderBy('id',$sort)->paginate($per_page);
            return $this->response->paginator($data, new DesignResultListTransformer)->setMeta($this->apiMeta());
        }
    }

    /**
     * @api {get} /designResults/alLists 所有上架设计成果列表
     * @author 王松
     * @apiVersion 1.0.0
     * @apiName designResultsAlLists
     * @apiGroup designResults
     * @apiParam {integer} sort 0:升序,1:降序(默认)
     * @apiParam {integer} page 页数
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
     *          "updated_at": 1540448935,
     *          "contacts": "羽落", //联系人
     *          "contact_number": 13217229788, //联系电话
     *          "design_company": {}, //设计公司信息
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
    public function alLists(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        if ($request->input('sort') == 0 && $request->input('sort') !== null) {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $list = DesignResult::where('status',3)->orderBy('id',$sort)->paginate($per_page);
        return $this->response->paginator($list, new DesignResultListTransformer())->setMeta($this->apiMeta());
    }

}








