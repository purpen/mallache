<?php
/**
 * 项目需求控制器
 *
 * @User llh
 * @time 2017-4-6
 */
namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\ItemListTransformer;
use App\Http\Transformer\ItemTransformer;
use App\Http\Transformer\RecommendListTransformer;
use App\Jobs\Recommend;
use App\Models\DesignCompanyModel;
use App\Models\DesignItemModel;
use App\Models\Item;
use App\Models\ItemRecommend;
use App\Models\ProductDesign;
use App\Models\UDesign;
use App\Models\User;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
     * @apiParam {string} type 设计类型：1.产品设计；2.UI UX 设计
     * @apiParam {integer} design_type 产品设计（1.产品策略；2.产品设计；3.结构设计；）UXUI设计（1.app设计；2.网页设计；）
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
        $type = (int)$request->input('type');

        //产品设计
        if($type === 1){

            $rules = [
                'type' => 'required|integer',
                'design_type' => 'required|integer',
                'field' => 'required|integer',
                'industry' => 'required|integer',
            ];

            $all = $request->only(['type','design_type','field', 'industry']);

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
                return $this->response->array($this->apiError('Error', 500));
            }

            return $this->response->array($this->apiSuccess());

        }
        //UX UI设计
        elseif ($type === 2){
            $rules = [
                'type' => 'require|integer',
                'design_type' => 'required|integer',
                'system' => 'required|integer',
                'design_content' => 'required|integer',
            ];

            $all = $request->only(['type', 'design_type', 'system', 'design_content']);

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
        "data": {
            "id": 13,
            "design_type": 1,
            "status": 3,
            "field": 1,
            "industry": 1,
            "name": "api UI",
            "product_features": "亮点",
            "competing_product": "竞品",
            "design_cost": 2, //设计费用：1、1万以下；2、1-5万；3、5-10万；4.10-20；5、20-30；6、30-50；7、50以上
            "province": 2,
            "city": 2
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
            return $this->response->array($this->apiSuccess());
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
     * @apiParam {string} type 设计类型：1.产品设计；2.UI UX 设计
     * @apiParam {integer} design_type 产品设计（1.产品策略；2.产品设计；3.结构设计；）UXUI设计（1.app设计；2.网页设计；）
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
        $type = (int)$request->input('type');

        //产品设计
        if($type === 1){

            $rules = [
                'type' => 'required|integer',
                'design_type' => 'required|integer',
                'field' => 'required|integer',
                'industry' => 'required|integer',
            ];

            $all = $request->only(['type', 'design_type', 'field', 'industry']);

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
        elseif ($type === 2){
            $rules = [
                'type' => 'required|integer',
                'design_type' => ['required', 'integer'],
                'system' => 'required|integer',
                'design_content' => 'required|integer',
            ];

            $all = $request->only(['type', 'design_type', 'system', 'design_content']);

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

    /**
     * @api {get} /demand/recommendList/{item_id} 项目ID获取推荐的设计公司
     * @apiVersion 1.0.0
     * @apiName demand recommendList
     * @apiGroup demandType
     *
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
    public function recommendList($item_id)
    {
        if(!$item = Item::find($item_id)){
            return $this->response->array($this->apiError('not found', 404));
        }

        //验证是否是当前用户对应的项目
        if($item->user_id !== $this->auth_user_id || $item->status !== 3){
            return $this->response->array($this->apiError('not found!', 404));
        }

        $recommend_arr = explode(',', $item->recommend);

        //如果推荐为空，则返回
        if(empty($recommend_arr)){
            return $this->response->array($this->apiSuccess('Success', 200, []));
        }

        $users = User::select('id')->whereIn('id', $recommend_arr)->get();

        return $this->response->collection($users, new RecommendListTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {post} /pushDemand 选定设计公司推送项目需求
     * @apiVersion 1.0.0
     * @apiName demand pushDemand
     * @apiGroup demandType
     *
     * @apiParam {string} token
     * @apiParam {integer} item_id 项目ID
     * @apiParam {array} design_company_id 设计公司ID
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function pushDemand(Request $request)
    {
        $rules = [
            'item_id' => 'required|integer',
            'design_company_id' => 'required|array',
        ];

        $all = $request->only(['item_id', 'design_company_id']);

        $validator = Validator::make($all, $rules);
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }


        try{
            //遍历插入推荐表
            foreach($all['design_company_id'] as $design_company_id)
            {
                ItemRecommend::create(['item_id' => $all['item_id'], 'design_company_id' => $design_company_id]);
            }
        }
        catch (\Exception $e){
            Log::error($e->getMessage());
            return $this->response->array($this->apiError('Error', 500));
        }

        return $this->response->array($this->apiSuccess());
    }

    //用户项目信息列表
    public function itemList()
    {
        $items = $this->auth_user->item;

        if($items->isEmpty()){
            return $this->response->array($this->apiSuccess());
        }

        return $this->response->collection($items, new ItemListTransformer )->setMeta($this->apiMeta());
    }

}
