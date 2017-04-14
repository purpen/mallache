<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\ColumnTransformer;
use App\Models\Column;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ColumnController extends BaseController
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
     * @api {post} /column 栏目位添加
     * @apiVersion 1.0.0
     * @apiName column store
     * @apiGroup column
     * @apiParam {integer} type 栏目类型
     * @apiParam {string} name 栏目名称
     * @apiParam {string} content 内容
     * @apiParam {string} url 链接
     * @apiParam {integer} sort 排序
     * @apiParam {integer} status 状态
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *     "data": {
     *       "id": 1,
     *       "type": 1,
     *       "name": "热门",
     *       "content": "今天的热门",
     *       "url": "www.baidu.com",
     *       "sort": 1,
     *       "status": 1
     *      },
     *     "meta": {
     *       "message": "",
     *       "status_code": 200
     *     }
     *   }
     *  }
     */
    public function store(Request $request)
    {
        $all = $request->all();
        $rules = [
            'type'  => 'required|integer',
            'name'  => 'required|max:50',
            'content'  => 'required|max:200',
            'url'  => 'required|max:200',
            'sort'  => 'required|integer',
            'status'  => 'required|integer',
        ];
        $messages = [
            'type.required' => '栏目类型不能为空',
            'name.required' => '栏目名称不能为空',
            'name.max' => '栏目名称最多50字符',
            'content.required' => '内容不能为空',
            'content.max' => '链接最多200字符',
            'url.required' => '链接不能为空',
            'url.max' => '链接最多200字符',
            'sort.required' => '排序不能为空',
            'status.required' => '状态不能为空'
        ];
        $validator = Validator::make($all, $rules, $messages);
        $all = $request->except(['token']);
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }
        try{
            $quotation = Column::firstOrCreate($all);
        }
        catch (\Exception $e){
            return $this->response->array($this->apiError());
        }

        return $this->response->item($quotation, new $column())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /column/1  根据栏目类型查看内容
     * @apiVersion 1.0.0
     * @apiName column show
     * @apiGroup column
     *
     * @apiParam {integer} type 栏目类型
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *   "columns": [
     *           {
     *              "id": 2,
     *              "type": 2,
     *              "name": "1",
     *              "content": "1",
     *              "url": "1",
     *              "sort": 1,
     *              "status": 1,
     *              "created_at": "2017-04-12 18:24:52",
     *              "updated_at": "2017-04-12 18:24:52"
     *          }
     *      ]
     *  }
     *
     */
    public function show(Request $request)
    {
        $type = $request->input('type');
        $column = Column::where('type' , $type)->get();
        Log::info($column);
        if(!$column){
            return $this->response->array($this->apiError());
        }
        return $this->response->item($column, new $column())->setMeta($this->apiMeta());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Column  $column
     * @return \Illuminate\Http\Response
     */
    public function edit(Column $column)
    {
        //
    }

    /**
     * @api {put} /column/1 根据栏目位id更新
     * @apiVersion 1.0.0
     * @apiName column update
     * @apiGroup column
     * @apiParam {integer} type 栏目类型
     * @apiParam {string} name 栏目名称
     * @apiParam {string} content 内容
     * @apiParam {string} url 链接
     * @apiParam {integer} sort 排序
     * @apiParam {integer} status 状态
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *     "data": {
     *       "id": 1,
     *       "type": 1,
     *       "name": "热门",
     *       "content": "今天的热门",
     *       "url": "www.baidu.com",
     *       "sort": 1,
     *       "status": 1
     *      },
     *     "meta": {
     *       "message": "",
     *       "status_code": 200
     *     }
     *   }
     *  }
     */
    public function update(Request $request , $id)
    {
        $all = $request->all();
        //验证规则
        $rules = [
            'type'  => 'required|integer',
            'name'  => 'required|max:50',
            'content'  => 'required|max:200',
            'url'  => 'required|max:200',
            'sort'  => 'required|integer',
            'status'  => 'required|integer',
        ];
        $messages = [
            'type.required' => '栏目类型不能为空',
            'name.required' => '栏目名称不能为空',
            'name.max' => '栏目名称最多50字符',
            'content.required' => '内容不能为空',
            'content.max' => '链接最多200字符',
            'url.required' => '链接不能为空',
            'url.max' => '链接最多200字符',
            'sort.required' => '排序不能为空',
            'status.required' => '状态不能为空'
        ];
        $validator = Validator::make($all, $rules, $messages);

        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        $all = $request->except(['token']);

        $column = Column::where('id', $id)->update($all);
        if(!$column){
            return $this->response->array($this->apiError());
        }
        return $this->response->array($this->apiSuccess());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Column  $column
     * @return \Illuminate\Http\Response
     */
    public function destroy(Column $column)
    {
        //
    }
}
