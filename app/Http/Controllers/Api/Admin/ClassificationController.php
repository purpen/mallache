<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\AdminTransformer\ClassificationTransformer;
use App\Models\Classification;
use Illuminate\Http\Request;

class ClassificationController extends BaseController
{
    /**
     * @api {get} /admin/classification/list 分类列表
     * @apiVersion 1.0.0
     * @apiName classification index
     * @apiGroup AdminClassification
     *
     * @apiParam {integer} type *栏目类型：1.文章；
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     * }
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'type' => 'required|integer'
        ]);
        $list = Classification::where('type', $request->input('type'))->get();

        return $this->response->collection($list, new ClassificationTransformer)->setMeta($this->apiMeta());
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
     * @api {post} /admin/classification 添加分类
     * @apiVersion 1.0.0
     * @apiName classification store
     * @apiGroup AdminClassification
     *
     * @apiParam {integer} type *栏目类型：1.文章；
     * @apiParam {string} name *分类名称
     * @apiParam {string} content 描述
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     * }
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'type' => 'required|integer',
            'name' => 'required|max:50',
            'content' => 'max:500',
        ]);

        $all = $request->all();

        $classification = Classification::create($all);
        if (!$classification) {
            return $this->response->array($this->apiError('error', 500));
        } else {
            return $this->response->array($this->apiSuccess());
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * @api {get} /admin/classification 分类信息详情
     * @apiVersion 1.0.0
     * @apiName classification edit
     * @apiGroup AdminClassification
     *
     * @apiParam {integer} id
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      },
     *      "data": {
     *      'name': name,     //
     *      'content': content,
     *      }
     * }
     */
    public function edit(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ]);
        if (!$classification = Classification::find($request->input('id'))) {
            return $this->response->array($this->apiError('not found', 404));
        } else {
            $data = [
                'id' => $classification->id,
                'type' => $classification->type,
                'name' => $classification->name,
                'content' => $classification->content,
            ];
            return $this->response->array($this->apiSuccess('Success', 200, $data));
        }

    }

    /**
     * @api {put} /admin/classification 修改分类
     * @apiVersion 1.0.0
     * @apiName classification update
     * @apiGroup AdminClassification
     *
     * @apiParam {integer} id
     * @apiParam {string} name 分类名称
     * @apiParam {string} content 描述
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     * }
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'name' => 'max:50',
            'content' => 'max:500',
        ]);

        $data = $request->only(['name', 'content']);
        if (!$classification = Classification::find($request->input('id'))) {
            return $this->response->array($this->apiError('not found', 404));
        } else {
            $classification->update($data);
            return $this->response->array($this->apiSuccess());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
