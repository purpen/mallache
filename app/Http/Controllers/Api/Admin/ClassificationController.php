<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\AdminTransformer\ClassificationTransformer;
use App\Models\Classification;
use Illuminate\Http\Request;

class ClassificationController extends BaseController
{

    /**
     * @api {get} /admin/classification/list  分类列表
     * @apiVersion 1.0.0
     * @apiName classification list
     * @apiGroup AdminClassification
     *
     * @apiParam {integer} page 页数
     * @apiParam {integer} per_page 页面条数
     * @apiParam {integer} type *栏目类型：0,全部；1.文章；
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     *      "data": [
     *      {
     *          "id": 1,
     *          "name": "单元测试1",
     *          "type": 1,
     *          "type_value": "文章",
     *          "status": 0,            // 0.禁用；1.启用；
     *      },
     *      ],
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
        $type = $request->input('type');

        $per_page = $request->input('per_page') ?? $this->per_page;

        $query = Classification::query();
        if ($type) {
            $query->where('type', $type);
        }

        $list = $query->paginate($per_page);

        return $this->response->paginator($list, new ClassificationTransformer)->setMeta($this->apiMeta());
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
     * @apiParam {integer} type *类型：1.文章；
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
     * @api {put} /admin/classification/changeStatus 变更分类状态
     * @apiVersion 1.0.0
     * @apiName classification changeStatus
     * @apiGroup AdminClassification
     *
     * @apiParam {integer} id 分类ID
     * @apiParam {integer} status 0.禁用；1.启用；
     * @apiParam {string} token
     * @apiSuccessExample 成功响应:
     *
     * {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     * }
     */
    public function changeStatus(Request $request)
    {
        $id = $request->input('id');
        if (!$classification = Classification::find((int)$id)) {
            return $this->response->array($this->apiError('not found!!!', 404));
        }

        if($request->input('status')){
            $classification->status = 1;
        }else{
            $classification->status = 0;
        }

        if(!$classification->save()){
            return $this->response->array($this->apiError('Error', 500));
        }

        return $this->response->array($this->apiSuccess());
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
