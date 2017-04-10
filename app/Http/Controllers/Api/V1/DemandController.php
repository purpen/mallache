<?php
/**
 * 项目需求控制器
 *
 * @User llh
 * @time 2017-4-6
 */
namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\ItemTransformer;
use App\Models\Item;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Item::find(intval($id));

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
            $item = Item::find(intval($id))->update($all);
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
}
