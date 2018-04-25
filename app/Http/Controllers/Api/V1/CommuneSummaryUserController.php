<?php

namespace App\Http\Controllers\Api\V1;



use App\Http\Transformer\CommuneSummaryUserTransformer;
use App\Http\Transformer\UserTaskUserTransformer;
use App\Models\CommuneSummaryUser;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CommuneSummaryUserController extends BaseController
{

    /**
     * @api {post} /communeSummaryUser 沟通纪要成员创建
     * @apiVersion 1.0.0
     * @apiName  communeSummaryUser store
     * @apiGroup communeSummaryUser
     *
     * @apiParam {integer} commune_summary_id 沟通纪要id
     * @apiParam {integer} selected_user_id 选择的用户
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
    {
    "data": {
    "id": 1,
    "commune_summary_id": 11,
    "user_id": 3,
    "type": 1,
    "status": 1,
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
        $this->validate($request, [
            'commune_summary_id' => 'required|integer',
        ]);
        $commune_summary_id = $request->input('commune_summary_id');
        $selected_user_id = $request->input('selected_user_id') ? $request->input('selected_user_id') : 0;

        $params = array(
            'commune_summary_id' => intval($commune_summary_id),
            'type' => 1,
            'status' => 1,
            'selected_user_id' => $selected_user_id,
            'user_id' => $this->auth_user_id,
        );
        //查看是否有没有创建过沟通纪要用户，有的话，跳出
        $communeSummaryUser = CommuneSummaryUser::where('commune_summary_id' , $commune_summary_id)->where('selected_user_id' , $selected_user_id)->first();
        if($communeSummaryUser){
            return $this->response->array($this->apiError('已存在该任务成员', 412));
        }
        $communeSummaryUsers= CommuneSummaryUser::create($params);

        return $this->response->item($communeSummaryUsers, new CommuneSummaryUserTransformer())->setMeta($this->apiMeta());

    }

    /**
     * @api {get} /communeSummaryUser/{id} 沟通纪要成员详情
     * @apiVersion 1.0.0
     * @apiName  communeSummaryUser show
     * @apiGroup communeSummaryUser
     *
     * @apiParam {string} token
     *
     *
     * @apiSuccessExample 成功响应:
    {
    "data": {
    "id": 1,
    "commune_summary_id": 11,
    "user_id": 3,
    "type": 1,
    "status": 1,
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
        $communeSummaryUser = CommuneSummaryUser::find($id);

        if (!$communeSummaryUser) {
            return $this->response->array($this->apiError('not found', 404));
        }

        return $this->response->item($communeSummaryUser, new CommuneSummaryUserTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /communeSummaryUser 沟通纪要成员列表
     * @apiVersion 1.0.0
     * @apiName  communeSummaryUser index
     * @apiGroup communeSummaryUser
     *
     * @apiParam {integer} commune_summary_id 沟通纪要id
     * @apiParam {int} sort 0:升序；1.降序(默认)
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
        {
            "data": [
                {
                    "id": 3,
                    "type": 2,
                    "account": "18132382133",
                    "username": "18132382133",
                    "email": null,
                    "phone": "18132382133",
                    "logo_image": null,
                    "design_company_id": 52,
                    "role_id": 0,
                    "realname": null,
                    "child_account": 1,
                    "company_role": 20,
                    "invite_user_id": 0,
                    "design_company_name": "2222",
                    "design_company_abbreviation": "qwqwqe",
                    "created_at": 1494551407
                },
                {
                    "id": 1,
                    "type": 2,
                    "account": "15810295774",
                    "username": "",
                    "email": null,
                    "phone": "15810295774",
                    "logo_image": null,
                    "design_company_id": 49,
                    "role_id": 20,
                    "realname": "1",
                    "child_account": 1,
                    "company_role": 20,
                    "invite_user_id": 0,
                    "design_company_name": "1",
                    "design_company_abbreviation": "1",
                    "created_at": 1492081381
                }
            ],
            "meta": {
                "message": "Success",
                "status_code": 200,
                "pagination": {
                    "total": 2,
                    "count": 2,
                    "per_page": 10,
                    "current_page": 1,
                    "total_pages": 1,
                    "links": []
                }
            }
        }
     */
    public function index(Request $request)
    {
        if($request->input('sort') == 0 && $request->input('sort') !== null) {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $commune_summary_id = $request->input('commune_summary_id');
        $communeSummaryUsers = CommuneSummaryUser::where('commune_summary_id' , $commune_summary_id)->orderBy('id', $sort)->get();
//        $user_id = [];
//        foreach ($communeSummaryUsers as $communeSummaryUser){
//            $user_id[] = $communeSummaryUser->selected_user_id;
//        }
//        $new_user_id = $user_id;
//        $users = User::whereIn('id',$new_user_id)->orderBy('id', $sort)->get();
//        return $this->response->collection($users, new UserTaskUserTransformer())->setMeta($this->apiMeta());
        return $this->response->collection($communeSummaryUsers, new CommuneSummaryUserTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {delete} /communeSummaryUser/{id} 沟通纪要成员删除
     * @apiVersion 1.0.0
     * @apiName communeSummaryUser delete
     * @apiGroup communeSummaryUser
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
        $communeSummaryUser = CommuneSummaryUser::find($id);
        //检验是否存在
        if (!$communeSummaryUser) {
            return $this->response->array($this->apiError('not found!', 404));
        }

        $ok = $communeSummaryUser->delete();
        if (!$ok) {
            return $this->response->array($this->apiError());
        }
        return $this->response->array($this->apiSuccess());
    }

}
