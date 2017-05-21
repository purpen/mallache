<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Admin\BaseController;
use App\Http\Transformer\ItemStageTransformer;
use App\Models\AssetModel;
use App\Models\Item;
use App\Models\ItemStage;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class ItemStageController extends BaseController
{
    /**
     * @api {get} /itemStage/item/lists 项目阶段展示
     * @apiVersion 1.0.0
     * @apiName itemStage lists
     * @apiGroup itemStage
     *
     * @apiParam {integer} item_id  项目id
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
            {
                "data": {
                "id": 2,
                "item_id": 2,
                "design_company_id": 49,
                "title": "哈哈",
                "content": "就是哈哈哈哈",
                "summary": "备注",
                "item_stage_image": []
                },
                "meta": {
                "message": "Success",
                "status_code": 200
                }
            }
     */
    public function lists(Request $request)
    {
        $item_id = $request->input('item_id');
        $itemStage = ItemStage::where('item_id' , $item_id)->get();
        if(!$itemStage){
            return $this->response->array($this->apiError('not found item_stage' , 404));
        }
        return $this->response->collection($itemStage, new ItemStageTransformer())->setMeta($this->apiMeta());

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
     * @api {post} /itemStage 保存项目阶段
     * @apiVersion 1.0.0
     * @apiName itemStage store
     * @apiGroup itemStage
     *
     * @apiParam {integer} item_id 项目id
     * @apiParam {string} title 项目阶段名称
     * @apiParam {string} content 内容描述
     * @apiParam {string} summary 备注
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
        {
            "data": {
            "id": 2,
            "item_id": 2,
            "design_company_id": 49,
            "title": "哈哈",
            "content": "就是哈哈哈哈",
            "summary": "备注",
            "item_stage_image": []
            },
            "meta": {
            "message": "Success",
            "status_code": 200
            }
        }
     */
    public function store(Request $request)
    {
        $all['item_id'] = $request->input('item_id');
        $all['title'] = $request->input('title');
        $all['content'] = $request->input('content');
        $all['summary'] = $request->input('summary') ?? '';

        $item = Item::where('id' , $request->input('item_id'))->first();
        if(!$item){
            return $this->response->array($this->apiError('not found item' , 404));
        }
        if($item->design_company_id != $this->auth_user->design_company_id){
            return $this->response->array($this->apiError('没有权限' , 403));
        }
        $all['design_company_id'] = $item->design_company_id;
        $rules = [
            'item_id' => 'required|integer',
            'title' => 'required',
            'content' => 'required',
        ];

        $messages = [
            'item_id.required' => '项目id不能为空' ,
            'title.required' => '项目阶段名称不能为空' ,
            'content.required' => '项目内容描述不能为空' ,
        ];
        $validator = Validator::make($all , $rules , $messages);

        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        try{
            if($item->status == 11){
                $itemStage = ItemStage::create($all);
                //附件
                $random = $request->input('random');
                AssetModel::setRandom($itemStage->id , $random);
            }else{
                return $this->response->array($this->apiError('项目还没有进行'));
            }
        } catch (\Exception $e) {
            return $this->response->array($this->apiError());
        }

        return $this->response->item($itemStage, new ItemStageTransformer())->setMeta($this->apiMeta());


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
        if(!$itemStage){
            return $this->response->array($this->apiError('not found', 404));
        }
        return $this->response->item($itemStage, new ItemStageTransformer())->setMeta($this->apiMeta());
    }

    /**
     */
    public function edit(ItemStage $itemStage)
    {
        //
    }

    /**
     * @api {put} /itemStage/{item_stage_id} 项目阶段更改
     * @apiVersion 1.0.0
     * @apiName itemStage put
     * @apiGroup itemStage
     *
     * @apiParam {integer} item_id 项目id
     * @apiParam {string} title 项目阶段名称
     * @apiParam {string} content 内容描述
     * @apiParam {string} summary 备注
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
        {
        "data": {
        "id": 2,
        "item_id": 2,
        "design_company_id": 49,
        "title": "哈哈",
        "content": "就是哈哈哈哈",
        "summary": "备注",
        "item_stage_image": []
        },
        "meta": {
        "message": "Success",
        "status_code": 200
        }
        }
     */
    public function update(Request $request, $id)
    {
        $all['item_id'] = $request->input('item_id');
        $all['title'] = $request->input('title');
        $all['content'] = $request->input('content');
        $all['summary'] = $request->input('summary') ?? '';

        $itemStage = ItemStage::where('id' , $id)->first();
        if(!$itemStage){
            return $this->response->array($this->apiError('not found item' , 404));
        }
        $item = Item::where('id' , $request->input('item_id'))->first();
        if(!$item){
            return $this->response->array($this->apiError('not found item' , 404));
        }
        if($item->design_company_id != $this->auth_user->design_company_id){
            return $this->response->array($this->apiError('没有权限' , 403));
        }
        $rules = [
            'item_id' => 'required|integer',
            'title' => 'required',
            'content' => 'required',
        ];

        $messages = [
            'item_id.required' => '项目id不能为空' ,
            'title.required' => '项目阶段名称不能为空' ,
            'content.required' => '项目内容描述不能为空' ,
        ];
        $validator = Validator::make($all , $rules , $messages);

        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }
        $itemStage->update($all);
        if(!$itemStage){
            return $this->response->array($this->apiError());
        }

        return $this->response->item($itemStage, new ItemStageTransformer())->setMeta($this->apiMeta());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ItemStage  $itemStage
     * @return \Illuminate\Http\Response
     */
    public function destroy(ItemStage $itemStage)
    {
        //
    }
}
