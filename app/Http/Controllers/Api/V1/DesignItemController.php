<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\DesignItemTransformer;
use App\Models\DesignItemModel;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DesignItemController extends BaseController
{
    /**
     * @api {get} /designItem 展示项目类别
     * @apiVersion 1.0.0
     * @apiName designItem index
     * @apiGroup designItem
     *
     * @apiParam {integer} user_id 用户ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *       "design_items": [
     *           {
     *               "id": 1,
     *               "user_id": 1,
     *               "good_field": 1,
     *               "project_cycle": 1,
     *               "min_price": "1.00",
     *               "max_price": "1.00",
     *               "created_at": "2017-04-07 16:50:30",
     *               "updated_at": "2017-04-07 16:50:30",
     *               "deleted_at": null
     *           },
     *           {
     *               "id": 2,
     *               "user_id": 1,
     *               "good_field": 2,
     *               "project_cycle": 2,
     *               "min_price": "2.00",
     *               "max_price": "2.00",
     *               "created_at": "2017-04-07 17:07:12",
     *              "updated_at": "2017-04-07 17:07:12",
     *               "deleted_at": null
     *           }
     *       ]
     *   }
     */
    public function index()
    {
        $user_id = intval($this->auth_user_id);
        $designItem = DesignItemModel::where('user_id', $user_id)->get();
        if(!$designItem){
            return $this->response->array($this->apiError());
        }
        return $this->response->item($designItem, new DesignItemTransformer())->setMeta($this->apiMeta());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * @api {post} /designItem 保存项目类型
     * @apiVersion 1.0.0
     * @apiName designItem store
     * @apiGroup designItem
     *
     * @apiParam {integer} good_field 擅长领域id
     * @apiParam {integer} project_cycle 项目周期
     * @apiParam {string} min_price 最低价格
     * @apiParam {string} max_price 最高价格
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": {
     *           "id": 1,
     *           "user_id": 1,
     *           "good_field": "1",
     *           "project_cycle": "1",
     *           "min_price": "1",
     *           "max_price": "1"
     *       },
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }
     *   }
     */
    public function store(Request $request)
    {
        $all = $request->all();
        $all['user_id'] = $this->auth_user_id;

        //验证规则
        $rules = [
            'good_field' => 'required|integer' ,
            'project_cycle' => 'required|integer' ,
            'min_price' => 'required' ,
            'max_price' => 'required'
        ];

        $messages = [
            'good_field.required' => '擅长领域id不能为空' ,
            'roject_cycle.required' => '项目周期不能为空' ,
            'min_price.required' => '最低价格不能为空' ,
            'max_price.required' => '最高价格不能为空'
        ];

        $validator = Validator::make($all , $rules , $messages);

        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        try{
            $designItem = DesignItemModel::create($all);
        } catch (\Exception $e) {
            throw new HttpException('Error');
        }

        return $this->response->item($designItem, new DesignItemTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /designItem/2  项目类型ID查看详情
     * @apiVersion 1.0.0
     * @apiName designItem show
     * @apiGroup designItem
     *
     * @apiParam {integer} id 项目类型ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *       {
     *           "data": {
     *              "id": 2,
     *              "user_id": 1,
     *              "good_field": "2",
     *              "project_cycle": "2",
     *              "min_price": "2.00",
     *              "max_price": "2.00"
     *          },
     *          "meta": {
     *              "message": "Success",
     *              "status_code": 200
     *          }
     *      }
     */
    public function show(Request $request)
    {
        $id = intval($request->input('id'));
        $designItem = DesignItemModel::where('id' , $id)->first();
        if(!$designItem){
            return $this->response->array($this->apiError());
        }
        return $this->response->item($designItem, new DesignItemTransformer())->setMeta($this->apiMeta());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\QuotationsItemModel  $quotationsItemModel
     * @return \Illuminate\Http\Response
     */
    public function edit(QuotationsItemModel $quotationsItemModel)
    {
        //
    }

    /**
     * @api {put} /designItem/1 更新项目类型
     * @apiVersion 1.0.0
     * @apiName designItem update
     * @apiGroup designItem
     *
     * @apiParam {integer} good_field 擅长领域id
     * @apiParam {integer} project_cycle 项目周期
     * @apiParam {string} min_price 最低价格
     * @apiParam {string} max_price 最高价格
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }
     *   }
     */
    public function update(Request $request , $id)
    {
        //验证规则
        $rules = [
            'good_field' => 'required|integer' ,
            'project_cycle' => 'required|integer' ,
            'min_price' => 'required' ,
            'max_price' => 'required'
        ];

        $messages = [
            'good_field.required' => '擅长领域id不能为空' ,
            'roject_cycle.required' => '项目周期不能为空' ,
            'min_price.required' => '最低价格不能为空' ,
            'max_price.required' => '最高价格不能为空'
        ];

        $validator = Validator::make($request->only(['good_field' , 'project_cycle' , 'min_price' , 'max_price']) , $rules , $messages);

        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        $all = $request->except(['token']);

        $designItem = DesignItemModel::where('id' , intval($id))->update($all);

        if(!$designItem){
            return $this->response->array($this->apiError());
        }
        return $this->response->array($this->apiSuccess());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\QuotationsItemModel  $quotationsItemModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(QuotationsItemModel $quotationsItemModel)
    {
        //
    }
}
