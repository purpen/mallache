<?php

namespace App\Http\Controllers\Api\V1;



use App\Http\Transformer\CommuneSummaryTransformer;
use App\Http\Transformer\CummuneSummaryTransformer;
use App\Models\CommuneSummary;
use App\Models\CommuneSummaryUser;
use App\Models\ItemUser;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Support\Facades\Validator;


class CommuneSummaryController extends BaseController
{

    /**
     * @api {post} /communeSummaries 沟通纪要创建
     * @apiVersion 1.0.0
     * @apiName  communeSummaries store
     * @apiGroup communeSummaries
     *
     * @apiParam {integer} item_id 项目id
     * @apiParam {string} title 标题
     * @apiParam {string} content 内容
     * @apiParam {string} location 定位
     * @apiParam {array}  selected_user_id 选择的用户id
     * @apiParam {string} expire_time 到期时间
     * @apiParam {string} random 随机数
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
    {
    "data": {
    "id": 1,
    "item_id": 11,
    "title": 3,
    "content": 1,
    "status": 1,
    "location": 1,
    "expire_time": 1,
    "user_id": 1,
    "created_at": 1522155497
    },
    "meta": {
    "message": "Success",
    "status_code": 200
    }
    }
     */
    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|max:100',
            'item_id' => 'required|integer',
        ];
        $messages = [
            'title.required' => '标题不能为空',
            'title.max' => '标题最多100字符',
            'item_id.required' => '项目id不能为空',
            'item_id.integer' => '项目id必须为整形',

        ];

        $content = $request->input('content') ? $request->input('content') : '';
        $location = $request->input('location') ? $request->input('location') : '';
        $expire_time = $request->input('expire_time') ? $request->input('expire_time') : null;
        $selected_user_id_arr = $request->input('selected_user_id') ? $request->input('selected_user_id') : [];

        $params = array(
            'title' => $request->input('title'),
            'item_id' => $request->input('item_id'),
            'user_id' => $this->auth_user_id,
            'status' => 1,
            'content' => $content,
            'location' => $location,
            'expire_time' => $expire_time,
        );

        $validator = Validator::make($params, $rules, $messages);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }
        $communeSummary = CommuneSummary::create($params);
        if($communeSummary){
            if ($random = $request->input('random')) {
                AssetModel::setRandom($communeSummary->id, $random);
            }
        }
        //如果选中的用户不为空，把用户更新到沟通纪要成员里
        if(!empty($selected_user_id_arr)) {
            foreach ($selected_user_id_arr as $selected_user_id) {
                //检查又没有创建过沟通纪要成员，创建过返回，没有创建过创建
                $find_commune_summary_user = CommuneSummaryUser::where('commune_summary_id' , $communeSummary->id)->where('selected_user_id' , $selected_user_id)->first();
                if($find_commune_summary_user){
                    continue;
                }else{
                    $task_user = new CommuneSummaryUser();
                    $task_user->user_id = $this->auth_user_id;
                    $task_user->commune_summary_id = $communeSummary->id;
                    $task_user->selected_user_id = $selected_user_id;
                    $task_user->type = 1;
                    $task_user->status = 1;
                    $task_user->save();
                }
            }
        }
        return $this->response->item($communeSummary, new CommuneSummaryTransformer())->setMeta($this->apiMeta());



    }

    /**
     * @api {get} /communeSummaries/{id} 沟通纪要详情
     * @apiVersion 1.0.0
     * @apiName  communeSummaries show
     * @apiGroup communeSummaries
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
    {
    "data": {
    "id": 1,
    "item_id": 11,
    "title": 3,
    "content": 1,
    "status": 1,
    "location": 1,
    "expire_time": 1,
    "user_id": 1,
    "created_at": 1522155497
    },
    "meta": {
    "message": "Success",
    "status_code": 200
    }
    }
     */
    public function show($id)
    {
        $communeSummary = CommuneSummary::find($id);

        if (!$communeSummary) {
            return $this->response->array($this->apiError('not found', 404));
        }

        return $this->response->item($communeSummary, new CommuneSummaryTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /communeSummaries 沟通纪要列表
     * @apiVersion 1.0.0
     * @apiName  communeSummaries index
     * @apiGroup communeSummaries
     *
     * @apiParam {integer} item_id 项目id
     * @apiParam {integer} per_page 分页数量
     * @apiParam {integer} page 页码
     * @apiParam {integer} sort 0:升序；1.降序(默认)
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
    {
    "data": {
    "id": 1,
    "item_id": 11,
    "title": 3,
    "content": 1,
    "status": 1,
    "location": 1,
    "expire_time": 1,
    "user_id": 1,
    "created_at": 1522155497
    },
    "meta": {
    "message": "Success",
    "status_code": 200
    }
    }
     */
    public function index(Request $request)
    {
        $item_id = $request->input('item_id') ? (int)$request->input('item_id') : 0;
        $per_page = $request->input('per_page') ?? $this->per_page;
        if($request->input('sort') == 0 && $request->input('sort') !== null)
        {
            $sort = 'asc';
        }
        else
        {
            $sort = 'desc';
        }
        $user_id = intval($this->auth_user_id);
        //检查是否有查看的权限
        $itemUser = ItemUser::checkUser($item_id , $user_id);
        if($itemUser == false){
            return $this->response->array($this->apiError('没有权限查看', 403));
        }
        $communeSummary = CommuneSummary::where('item_id' , $item_id)->orderBy('id', $sort)->paginate($per_page);
        return $this->response->paginator($communeSummary, new CommuneSummaryTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {delete} /communeSummaries/{id} 沟通纪要删除
     * @apiVersion 1.0.0
     * @apiName communeSummaries delete
     * @apiGroup communeSummaries
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
     */
    public function destroy($id)
    {
        $communeSummary = CommuneSummary::find($id);
        //检验是否存在
        if (!$communeSummary) {
            return $this->response->array($this->apiError('not found!', 404));
        }

        $ok = $communeSummary->delete();
        if (!$ok) {
            return $this->response->array($this->apiError());
        }
        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {put} /communeSummaries/{id} 沟通纪要更改
     * @apiVersion 1.0.0
     * @apiName  communeSummaries update
     * @apiGroup communeSummaries
     *
     * @apiParam {string} title 标题
     * @apiParam {string} content 内容
     * @apiParam {string} location 定位
     * @apiParam {array}  selected_user_id 选择的用户id
     * @apiParam {string} expire_time 到期时间
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
    {
    "data": {
    "id": 1,
    "item_id": 11,
    "title": 3,
    "content": 1,
    "status": 1,
    "location": 1,
    "expire_time": 1,
    "user_id": 1,
    "created_at": 1522155497
    },
    "meta": {
    "message": "Success",
    "status_code": 200
    }
    }
     */
    public function update(Request $request , $id)
    {
        $all = $request->except(['token']);
        //检验是否存在沟通纪要
        $communeSummary = CommuneSummary::find($id);
        if (!$communeSummary) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        $new_all = array_diff($all , array(null));
        $communeSummary->update($new_all);
        if (!$communeSummary) {
            return $this->response->array($this->apiError());
        }
        return $this->response->item($communeSummary, new CommuneSummaryTransformer())->setMeta($this->apiMeta());
    }
}
