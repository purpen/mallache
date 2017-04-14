<?php
/**
 * 项目需求控制器
 *
 * @User llh
 * @time 2017-4-6
 */
namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\ItemTransformer;
use App\Jobs\Recommend;
use App\Models\Item;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class DemandController extends BaseController
{
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
    /**
     * @api {post} /demand 添加项目类型、领域
     * @apiVersion 1.0.0
     * @apiName demand store
     * @apiGroup demandType
     *
     * @apiParam {string} token
     * @apiParam {integer} design_type 设计类别：1.产品策略；2.产品设计；3.结构设计；4.app设计；5.网页设计；
     * @apiParam {integer} field 所属领域
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "data": {
     *
     *      },
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function store(Request $request)
    {
        //表单验证
        $rules = [
            'design_type' => [Rule::in([1, 2, 3, 4, 5])],
            'field' => 'integer',
        ];

        $all = $request->only(['design_type', 'field']);

        $validator = Validator::make($all, $rules);
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }
        $all['user_id'] = $this->auth_user_id;
        $all['status'] = 1;

        try{
            $item = Item::create($all);
        }
        catch (\Exception $e){
            return $this->response->array($this->apiError('Error', 500));
        }

        return $this->response->item($item, new ItemTransformer)->setMeta($this->apiMeta());
    }


    /**
     * @api {get} /demand/{id} 获取项目类型、领域、详细信息
     * @apiVersion 1.0.0
     * @apiName demand show
     * @apiGroup demandType
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
        "data": {
            "id": 1,
            "design_type": 4,  //设计类型
            "field": 4,  //领域
            "status": 2, //状态：1.填写资料；2.人工干预；3.推荐；4.发布；5失败；6成功；7.取消；
            "info": {
                "id": 2,
                "item_id": 1, //项目ID
                "system": 1,  //系统：1.ios；2.安卓；
                "design_content": 0,
                "page_number": 0,
                "name": "",
                "stage": 0,
                "complete_content": 0,
                "other_content": "",
                "style": 0,
                "start_time": 0,
                "cycle": 0,
                "design_cost": 0,
                "province": 0,
                "city": 0,
                "summary": "",
                "artificial": 0,
                "created_at": "2017-04-06 18:03:16",
                "updated_at": "2017-04-06 18:03:16"
             }
        },
        "meta": {
            "message": "Success",
            "status_code": 200
        }
    }
     */
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(!$item = Item::find(intval($id))){
            return $this->response->array($this->apiError('not found!', 404));
        }
        //验证是否是当前用户对应的项目
        if($item->user_id !== $this->auth_user_id){
            return $this->response->array($this->apiError('not found!', 404));
        }

        if(!$item){
            return $this->response->array($this->apiError());
        }
        return $this->response->item($item, new ItemTransformer)->setMeta($this->apiMeta());
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
     * @api {put} /demand/{id} 更改项目类型、领域
     * @apiVersion 1.0.0
     * @apiName demand update
     * @apiGroup demandType
     *
     * @apiParam {string} token
     * @apiParam {integer} design_type 设计类别：1.产品策略；2.产品设计；3.结构设计；4.app设计；5.网页设计；
     * @apiParam {integer} field 所属领域
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "data": {
     *
     *      },
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
    public function update(Request $request, $id)
    {
        //表单验证
        $rules = [
            'design_type' => [Rule::in([1, 2, 3, 4, 5])],
            'field' => 'integer',
        ];

        $all = $request->only(['design_type', 'field']);

        $validator = Validator::make($all, $rules);
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        $all['status'] = 1;
        try{

            if(!$item = Item::find(intval($id))){
                return $this->response->array($this->apiError('not found!', 404));
            }
            //验证是否是当前用户对应的项目
            if($item->user_id !== $this->auth_user_id){
                return $this->response->array($this->apiError('not found!', 404));
            }

            $item->update($all);
        }
        catch (\Exception $e){
            return $this->response->array($this->apiError('Error', 500));
        }

        return $this->response->array($this->apiSuccess());
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

    /**
     * @api {post} /demand/release 发布项目
     * @apiVersion 1.0.0
     * @apiName demand release
     * @apiGroup demandType
     *
     * @apiParam {string} token
     * @apiParam {integer} id 项目ID
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function release(Request $request)
    {
        $id = (int)$request->input('id');
        if(!$item = Item::find($id)){
            return $this->response->array($this->apiError('not found', 404));
        }

        //验证是否是当前用户对应的项目
        if($item->user_id !== $this->auth_user_id){
            return $this->response->array($this->apiError('not found!', 404));
        }

        try{
            $item->status = 2;
            $item->save();
        }
        catch (\Exception $e){
            return $this->response->array($this->apiError('Error', 500));
        }
        dispatch(new Recommend($item));
        return $this->response->array($this->apiSuccess());
    }

    public function getRecommend($item_id)
    {
        if(!$item = Item::find((int)$item_id)){

        }
    }
}
