<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Transformer\TagTransformer;
use App\Http\Transformer\TaskTransformer;
use App\Models\Tag;
use App\Models\Task;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TagController extends BaseController
{
    /**
     * @api {get} /tags  标签列表
     * @apiVersion 1.0.0
     * @apiName tags index
     * @apiGroup tags
     *
     * @apiParam {integer} item_id 项目id;
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     *   {
     *    "data": {
     *      "id": 23,
     *      "user_id": 12,
     *      "item_id": 22,  //项目id
     *      "title": 33,   //标题
     *      "type": [],   // 类型
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
        $item_id = $request->input('item_id') ? $request->input('item_id') : 0;
        $tags = Tag::where('item_id' , $item_id)->orderBy('id', 'desc')->get();

        return $this->response->collection($tags, new TagTransformer())->setMeta($this->apiMeta());

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
     * @api {post} /tags 创建
     * @apiVersion 1.0.0
     * @apiName  tags store
     * @apiGroup tags
     * @apiParam {string} title 标题
     * @apiParam {integer} type 类型:1.默认;
     * @apiParam {integer} item_id 项目ID
     * @apiParam {integer} task_id 项目ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     *   {
     *    "data": {
     *      "id": 23,
     *      "user_id": 12,
     *      "item_id": 22,  //项目id
     *      "title": 33,   //标题
     *      "type": [],   // 类型
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
            'item_id.required' => '项目id不能为空',
            'title.max' => '最多100字符',
        ];


        $type = $request->input('type') ? (int)$request->input('type') : 0;
        $item_id = $request->input('item_id');
        $task_id = $request->input('task_id') ? (int)$request->input('task_id') : 0;

        $params = array(
            'title' => $request->input('title'),
            'user_id' => $this->auth_user_id,
            'type' => $type,
            'item_id' => $item_id,
            'task_id' => $task_id,
        );

        $validator = Validator::make($params, $rules, $messages);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }


        try {
            $tags = Tag::create($params);

        } catch (\Exception $e) {

            throw new HttpException($e->getMessage());
        }

        return $this->response->item($tags, new TagTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /tags/{id}  详情
     * @apiVersion 1.0.0
     * @apiName tags show
     * @apiGroup tags
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     *   {
     *    "data": {
     *      "id": 23,
     *      "user_id": 12,
     *      "item_id": 22,  //项目id
     *      "title": 33,   //标题
     *      "type": [],   // 类型
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
        $tags = Tag::find($id);

        if (!$tags) {
            return $this->response->array($this->apiError('not found', 404));
        }

        return $this->response->item($tags, new TagTransformer())->setMeta($this->apiMeta());


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
     * @api {put} /tags/{id} 更新
     * @apiVersion 1.0.0
     * @apiName tags update
     * @apiGroup tags
     * @apiParam {string} title 标题
     * @apiParam {integer} type 类型:1.默认;
     * @apiParam {integer} item_id 项目ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     *   {
     *    "data": {
     *      "id": 23,
     *      "user_id": 12,
     *      "item_id": 22,  //项目id
     *      "title": 33,   //标题
     *      "type": [],   // 类型
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

        $type = $request->input('type') ? (int)$request->input('type') : 0;
        $item_id = $request->input('item_id') ? (int)$request->input('item_id') : 0;
        $task_id = $request->input('task_id') ? (int)$request->input('task_id') : 0;

        $params = array(
            'title' => $request->input('title'),
            'user_id' => $this->auth_user_id,
            'type' => $type,
            'item_id' => $item_id,
            'task_id' => $task_id,
        );

        $validator = Validator::make($params, $rules, $messages);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        //检验是否存在该作品
        $tags = Tag::find($id);
        if (!$tags) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        //检验是否是当前用户创建的案例
        if ($tags->user_id != $this->auth_user_id) {
            return $this->response->array($this->apiError('没有权限更改!', 403));
        }
        $tags->update($params);
        if (!$tags) {
            return $this->response->array($this->apiError());
        }
        return $this->response->item($tags, new TagTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {delete} /tags/{id} 删除
     * @apiVersion 1.0.0
     * @apiName tags delete
     * @apiGroup tags
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *     "meta": {
     *       "message": "",
     *       "status_code": 200
     *     }
     *   }
     *
     */
    public function destroy($id)
    {
        //检验是否存在
        $tag = Tag::find($id);
        if (!$tag) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        //检验是否是当前用户创建的作品
        if ($tag->user_id != $this->auth_user_id) {
            return $this->response->array($this->apiError('没有权限删除!', 403));
        }
        $ok = $tag->delete();
        if ($ok) {
            $new_task_id_arr = Tag::tagTask($id);
            //获取所有有关标签的任务
            $new_tasks = Task::whereIn('id' , $new_task_id_arr)->get();
            foreach ($new_tasks as $new_task){
                //获取单个任务标签
                $tags = $new_task->tags;
                //单个标签转换成数组
                $new_tags = explode(',' , $tags);
                foreach ($new_tags as $k=>$v){
                    if($id == $v) unset($new_tags[$k]);
                    $new_task->tags = implode(',' , $new_tags);
                    $new_task->save();
                }

            }
            return $this->response->array($this->apiSuccess());

        }
    }

    /**
     * @api {get} /tags/task/{id} 标签id查看任务
     * @apiVersion 1.0.0
     * @apiName tags tagTask
     * @apiGroup tags
     *
     * @apiParam {string} token
     *
     *
     */
    public function tagTask($id)
    {
        $tag = Tag::find($id);
        if(!$tag){
            return $this->response->array($this->apiError('没有找到该标签!', 404));
        }
        $new_task_id_arr = Tag::tagTask($id);

        //获取所有有关标签的任务
        $new_tasks = Task::whereIn('id' , $new_task_id_arr)->get();

        return $this->response->collection($new_tasks, new TaskTransformer())->setMeta($this->apiMeta());


    }

}
