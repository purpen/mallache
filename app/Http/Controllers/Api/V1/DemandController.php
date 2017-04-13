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
use App\Models\ProductDesign;
use App\Models\UDesign;
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
     * @apiParam {integer} industry 行业
     * @apiParam {integer} system 系统：1.ios；2.安卓；
     * @apiParam {integer} design_content 设计内容：1.视觉设计；2.交互设计；
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
        $design_type = $request->input('design_type');

        //产品设计
        if(in_array($design_type, [1, 2, 3])){

            $rules = [
                'design_type' => ['required', Rule::in([1, 2, 3])],
                'field' => 'required|integer',
                'industry' => 'required|integer',
            ];

            $all = $request->only(['design_type','field', 'industry']);

            $validator = Validator::make($all, $rules);
            if($validator->fails()){
                throw new StoreResourceFailedException('Error', $validator->errors());
            }

            $all['user_id'] = $this->auth_user_id;
            $all['status'] = 1;



            try{
                $item = Item::create($all);

                $product_design = ProductDesign::create([
                    'item_id' => intval($item->id),
                    'field' => $request->input('field'),
                    'industry' => $request->input('industry')
                ]);
            }
            catch (\Exception $e){
                dd($e);
                return $this->response->array($this->apiError('Error', 500));
            }

            return $this->response->array($this->apiSuccess());

        }
        //UX UI设计
        elseif (in_array($design_type, [4, 5])){
            $rules = [
                'design_type' => ['required', Rule::in([4, 5])],
                'system' => 'required|integer',
                'design_content' => 'required|integer',
            ];

            $all = $request->only(['design_type','system','design_content']);

            $validator = Validator::make($all, $rules);
            if($validator->fails()){
                throw new StoreResourceFailedException('Error', $validator->errors());
            }

            $all['user_id'] = $this->auth_user_id;
            $all['status'] = 1;

            try{
                $item = Item::create($all);

                $u_design = UDesign::create([
                    'item_id' => intval($item->id),
                    'system' => $request->input('system'),
                    'design_content' => $request->input('design_content')
                ]);
            }
            catch (\Exception $e){
                dd($e);
                return $this->response->array($this->apiError('Error', 500));
            }

            return $this->response->array($this->apiSuccess());
        }else{
            return $this->response->array($this->apiError('not found', 404));
        }

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
     * @apiParam {integer} industry 行业
     * @apiParam {integer} system 系统：1.ios；2.安卓；
     * @apiParam {integer} design_content 设计内容：1.视觉设计；2.交互设计；
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
    public function update(Request $request, $id)
    {
        $design_type = $request->input('design_type');

        //产品设计
        if(in_array($design_type, [1, 2, 3])){

            $rules = [
                'design_type' => ['required', Rule::in([1, 2, 3])],
                'field' => 'required|integer',
                'industry' => 'required|integer',
            ];

            $all = $request->only(['design_type', 'field', 'industry']);

            $validator = Validator::make($all, $rules);
            if($validator->fails()){
                throw new StoreResourceFailedException('Error', $validator->errors());
            }

            try{

                if(!$item = Item::find(intval($id))){
                    return $this->response->array($this->apiError('not found!', 404));
                }
                //验证是否是当前用户对应的项目
                if($item->user_id !== $this->auth_user_id){
                    return $this->response->array($this->apiError('not found!', 404));
                }

                $item->update($all);

                $product_design = ProductDesign::firstOrCreate(['item_id' => intval($item->id)]);
                $product_design->field = $request->input('field');
                $product_design->industry = $request->input('industry');
                $product_design->save();
            }
            catch (\Exception $e){
                return $this->response->array($this->apiError('Error', 500));
            }

            return $this->response->array($this->apiSuccess());

        }
        //UX UI设计
        elseif (in_array($design_type, [4, 5])){
            $rules = [
                'design_type' => ['required', Rule::in([4, 5])],
                'system' => 'required|integer',
                'design_content' => 'required|integer',
            ];

            $all = $request->only(['design_type', 'system', 'design_content']);

            $validator = Validator::make($all, $rules);
            if($validator->fails()){
                throw new StoreResourceFailedException('Error', $validator->errors());
            }

            try{
                if(!$item = Item::find(intval($id))){
                    return $this->response->array($this->apiError('not found!', 404));
                }
                //验证是否是当前用户对应的项目
                if($item->user_id !== $this->auth_user_id){
                    return $this->response->array($this->apiError('not found!', 404));
                }

                $item->update($all);

                $product_design = UDesign::firstOrCreate(['item_id' => intval($item->id)]);
                $product_design->system = $request->input('system');
                $product_design->design_content = $request->input('design_content');
                $product_design->save();
            }
            catch (\Exception $e){
                return $this->response->array($this->apiError('Error', 500));
            }

            return $this->response->array($this->apiSuccess());
        }else{
            return $this->response->array($this->apiError('not found', 404));
        }

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

}
