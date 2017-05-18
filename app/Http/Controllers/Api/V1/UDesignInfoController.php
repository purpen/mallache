<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\ItemTransformer;
use App\Models\Item;
use App\Models\ProductDesign;
use App\Models\UDesign;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UDesignInfoController extends BaseController
{
    /*
    system	tinyint(4)	是		系统：1.ios；2.安卓；
    design_content	tinyint(4)	是		设计内容：1.视觉设计；2.交互设计；
    page_number	tinyint(4)	是		页面数量：1.10页以内；2.10-30'；3.30-50;4.50-；
    name	varchar(50)	是		项目名称
    stage	tinyint(4)	是		阶段：1、已有app／网站，需重新设计；2、没有app／网站，需要全新设计；
    complete_content	tinyint(4)	是		已完成设计内容：1.流程图；2.线框图；3.页面内容；4.产品功能需求点；5.其他
    other_content	varchar(20)	是		其他设计内容
    style	tinyint(4)	是		ui设计风格：1.简约；2.扁平化；3.拟物；
    start_time	tinyint(4)	是		项目开始时间：1、可商议；2、马上开始；3、有具体时间；
    cycle	tinyint(4)	是		项目周期：1.1-2周；2.2-4周；3.1-2月；4.2月以上；5.不确定
    design_cost	tinyint(4)	是		设计费用：
    province	int(10)	是		省份
    city	int(10)	是		城市
    summary	varchar(500)	是		备注
    artificial	tinyint(4)	是	0	人工服务*/

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }


    /**
     * Display the specified resource.
     *
     * @param  int  $item_id
     * @return \Illuminate\Http\Response
     */
    public function show($item_id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * @api {put} /UDesign/{item_id} 更改 UX UI详细信息
     * @apiVersion 1.0.0
     * @apiName demand update
     * @apiGroup demandUDesign
     *
     * @apiParam {integer} stage_status //资料填写阶段；1.项目类型；2.需求信息；3.公司信息
     * @apiParam {string} name 项目名称
     * @apiParam {integer} stage 阶段：1、已有app／网站，需重新设计；2、没有app／网站，需要全新设计；
     * @apiParam {array} complete_content 已完成设计内容：
     * @apiParam {integer} cycle 设计周期：1.1个月内；2.1-2个月；3.2个月；4.2-4个月；5.其他
     * @apiParam {integer} design_cost 设计费用：1、1-5万；2、5-10万；3.10-20；4、20-30；5、30-50；6、50以上
     * @apiParam {integer} province 省份
     * @apiParam {integer} city 城市
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $item_id)
    {
        $all = $request->all();
        $rules = [
            'name' => 'required|max:50',
            'stage' => ['required', 'integer', Rule::in([1, 2])],
            'complete_content' => 'array',
            'design_cost' => ['required', 'integer'],
            'province' => 'required|integer',
            'city' => 'required|integer',
            'cycle' => 'required|integer',
        ];
        $validator = Validator::make($all, $rules);
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        try{
            if(!$item = Item::find($item_id)){
                return $this->response->array($this->apiError('not found!', 404));
            }

            //验证是否是当前用户对应的项目
            if($item->user_id !== $this->auth_user_id || $item->type != 2 || 1 != $item->status){
                return $this->response->array($this->apiError('not found!', 404));
            }

            $item->stage_status = $request->input('stage_status') ?? 2;
            $item->save();

            $design = UDesign::where(['item_id' => intval($item_id)])->first();

            $all['complete_content'] = implode('&', $all['complete_content']);
            $all['other_content'] = $request->input('other_content') ?? '';

            $design->update($all);
        }catch(\Exception $e){
            return $this->response->array($this->apiError('Error', 500));
        }

        return $this->response->item($item, new ItemTransformer)->setMeta($this->apiMeta());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
