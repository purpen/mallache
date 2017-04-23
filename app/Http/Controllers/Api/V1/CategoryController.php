<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\CategoryTransformer;
use App\Models\Category;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CategoryController extends BaseController
{
    /**
     * @api {get} /category 查看分类
     * @apiVersion 1.0.0
     * @apiName category index
     * @apiGroup category
     *
     * @apiParam {integer} type 分类类型
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *       "data": [
     *       {
     *           "id": 1,
     *           "type": 1,
     *           "name": "电器",
     *           "pid": 0,
     *           "status": 1
     *       },
     *       {
     *           "id": 2,
     *           "type": 1,
     *           "name": "电冰箱",
     *           "pid": 0,
     *           "status": 1
     *       }
     *       ],
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }
     *   }
     */
    public function index(Request $request)
    {
        $type = $request->input('type');
        $category = Category::where('type' , $type)->get();
        if(!$category){
            return $this->response->array($this->apiSuccess());
        }
        return $this->response->collection($category, new CategoryTransformer())->setMeta($this->apiMeta());
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
     * @api {post} /category 分类添加
     * @apiVersion 1.0.0
     * @apiName category store
     * @apiGroup category
     * @apiParam {integer} type 分类类型
     * @apiParam {string} name 分类名称
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *       "data": {
     *       "id": 4,
     *       "type": 6,
     *       "name": "电器",
     *       "pid": 0,
     *       "status": 0
     *       },
     *       "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *       }
     *}
     *
     */
    public function store(Request $request)
    {
        $all = $request->all();
        $rules = [
            'type'  => 'required|integer',
            'name'  => 'required|max:20',
        ];
        $messages = [
            'type.required' => '分类类型不能为空',
            'name.required' => '分类名称不能为空',
            'name.max' => '分类名称最多20字符',
        ];
        $validator = Validator::make($all, $rules, $messages);
        $all = $request->except(['token']);

        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }
        try{
            $category = Category::firstOrCreate($all);
        }
        catch (\Exception $e){
            return $this->response->array($this->apiError());
        }

        return $this->response->item($category, new CategoryTransformer())->setMeta($this->apiMeta());
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * @api {put} /category/{id} 分类更改
     * @apiVersion 1.0.0
     * @apiName category update
     * @apiGroup category
     * @apiParam {integer} type 分类类型
     * @apiParam {string} name 分类名称
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *     "meta": {
     *       "message": "",
     *       "status_code": 200
     *     }
     *   }
     */
    public function update(Request $request , $id)
    {
        $all = $request->except(['token']);

        $rules = [
            'type'  => 'required|integer',
            'name'  => 'required|max:20',
        ];
        $messages = [
            'type.required' => '分类类型不能为空',
            'name.required' => '分类名称不能为空',
            'name.max' => '分类名称最多20字符',
        ];
        $validator = Validator::make($all, $rules, $messages);

        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        $category = Category::where('id' , $id)->update($all);
        if(!$category){
            return $this->response->array($this->apiError());
        }
        return $this->response->array($this->apiSuccess());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        //
    }
}
