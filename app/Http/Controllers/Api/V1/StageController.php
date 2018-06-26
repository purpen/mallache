<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\StageTransformer;
use App\Models\Stage;
use App\Models\Task;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StageController extends BaseController
{
    /**
     * @api {get} /stages  阶段列表
     * @apiVersion 1.0.0
     * @apiName stages index
     * @apiGroup stages
     *
     * @apiParam {integer} item_id 项目id;
     * @apiParam {integer} stage 默认10；0.全部 2.已完成 -1.未完成  默认0
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     *   {
     *    "data": {
     *      "id": 23,
     *      "item_id": 12,
     *      "title": 22,
     *      },
     *      "meta": {
     *      "message": "Success",
     *      "status_code": 200
     *      }
     *   }
     *
     */
    public function index(Request $request)
    {
        $item_id = $request->input('item_id');
        $stage_status = $request->input('stage') ? intval($request->input('stage')) : 0;
        $stages = Stage::where('item_id' , $item_id)->orderBy('id', 'desc')->get();
        foreach ($stages as $stage){
            $stage->stage = $stage_status;
        }
        return $this->response->collection($stages, new StageTransformer())->setMeta($this->apiMeta());

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
     * @api {post} /stages 创建
     * @apiVersion 1.0.0
     * @apiName  stages store
     * @apiGroup stages
     * @apiParam {string} title 标题
     * @apiParam {integer} item_id 项目ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     *   {
     *    "data": {
     *      "id": 23,
     *      "item_id": 12,
     *      "title": 22,
     *      },
     *      "meta": {
     *      "message": "Success",
     *      "status_code": 200
     *      }
     *   }
     *
     */
    public function store(Request $request)
    {
        // 验证规则
        $rules = [
            'title' => 'required|max:100',
            'item_id' => 'required|integer',
        ];
        $messages = [
            'title.required' => '标题不能为空',
            'title.max' => '最多100字符',
            'item_id.required' => '项目id不能为空',
            'item_id.integer' => '项目id为整型',
        ];

        $params = array(
            'title' => $request->input('title'),
            'item_id' => $request->input('item_id'),
        );

        $validator = Validator::make($params, $rules, $messages);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }
        $stage = Stage::create($params);

        return $this->response->item($stage, new StageTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /stages/{id}  详情
     * @apiVersion 1.0.0
     * @apiName stages show
     * @apiGroup stages
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     *   {
     *    "data": {
     *      "id": 23,
     *      "item_id": 12,
     *      "title": 22,
     *      },
     *      "meta": {
     *      "message": "Success",
     *      "status_code": 200
     *      }
     *   }
     *
     */
    public function show($id)
    {
        $id = intval($id);
        $stage = Stage::find($id);

        if (!$stage) {
            return $this->response->array($this->apiError('not found', 404));
        }

        return $this->response->item($stage, new StageTransformer())->setMeta($this->apiMeta());


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DesignCaseModel $designCaseModel
     * @return \Illuminate\Http\Response
     */
    public function edit(DesignCaseModel $works)
    {
        //
    }

    /**
     * @api {put} /stages/{id} 更新
     * @apiVersion 1.0.0
     * @apiName stages update
     * @apiGroup stages
     * @apiParam {string} title 标题
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     *   {
     *    "data": {
     *      "id": 23,
     *      "item_id": 12,
     *      "title": 22,
     *      },
     *      "meta": {
     *      "message": "Success",
     *      "status_code": 200
     *      }
     *   }
     *
     */
    public function update(Request $request, $id)
    {
        // 验证规则
        $rules = [
            'title' => 'required|max:100',
        ];
        $messages = [
            'title.required' => '标题不能为空',
            'title.max' => '最多100字符',
        ];

        $params = array(
            'title' => $request->input('title'),
        );

        $validator = Validator::make($params, $rules, $messages);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        $validator = Validator::make($params, $rules, $messages);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        //检验是否存在该作品
        $stage = Stage::find($id);
        if (!$stage) {
            return $this->response->array($this->apiError('not found!', 404));
        }

        $stage->update($params);
        if (!$stage) {
            return $this->response->array($this->apiError());
        }
        return $this->response->item($stage, new StageTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {delete} /stages/{id} 删除
     * @apiVersion 1.0.0
     * @apiName stages delete
     * @apiGroup stages
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     *   {
     *    "data": {
     *      "id": 23,
     *      "item_id": 12,
     *      "title": 22,
     *      },
     *      "meta": {
     *      "message": "Success",
     *      "status_code": 200
     *      }
     *   }
     *
     */
    public function destroy($id)
    {
        //检验是否存在
        $stage = Stage::find($id);
        if (!$stage) {
            return $this->response->array($this->apiError('not found!', 404));
        }

        $ok = $stage->delete();
        if (!$ok) {
            return $this->response->array($this->apiError());
        }
        return $this->response->array($this->apiSuccess());
    }

}
