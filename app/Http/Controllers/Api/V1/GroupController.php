<?php

namespace App\Http\Controllers\Api\V1;


use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * 用户群组
 *
 * Class GroupController
 * @package App\Http\Controllers\Api\V1
 */
class GroupController extends BaseController
{
    /**
     * @api {get} /group/lists  获取公司所有自己创建的群组列表（设计公司管理员）
     * @apiVersion 1.0.0
     * @apiName group lists
     *
     * @apiGroup group
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     * "meta": {
     * "message": "Success",
     * "status_code": 200
     * },
     * "data": [
     *      {
     *          "id": 1, // 群组ID
     *          "name": "测试一个群组",
     *          "type": 2   // 群组类型：1.系统创建 2. 用户创建
     *      }
     * ]
     */
    public function lists()
    {
        // 判断是否是设计公司管理员
        if (!$this->auth_user->isDesignAdmin()) {
            return $this->response->array($this->apiError('无权限', 403));
        }

        $company_id = User::designCompanyId($this->auth_user_id);


        $lists = Group::groupList($company_id);

        return $this->response->array($this->apiSuccess('Success', 200, $lists->toArray()));
    }

    /**
     * @api {post} /group/create  创建群组（设计公司管理员）
     * @apiVersion 1.0.0
     * @apiName group create
     *
     * @apiGroup group
     * @apiParam {string} token
     * @apiParam {string} name 群组名称
     * @apiParam {array} user_id_arr 用户ID数组
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     *  }
     */
    public function create(Request $request)
    {
        // 判断是否是设计公司管理员
        if (!$this->auth_user->isDesignAdmin()) {
            return $this->response->array($this->apiError('无权限', 403));
        }

        $this->validate($request, [
            'name' => 'required|max:50',
            'user_id_arr' => 'required|array',
        ]);

        $name = $request->input('name');
        $user_id_arr = $request->input('user_id_arr');
        $company_id = User::designCompanyId($this->auth_user_id);

        if (Group::createGroup($name, $user_id_arr, 2, $company_id)) {
            return $this->response->array($this->apiSuccess());
        } else {
            return $this->response->array($this->apiError('server error', 500));
        }
    }


    /**
     * @api {put} /group/addUser  向群组添加用户（设计公司管理员）
     * @apiVersion 1.0.0
     * @apiName group addUser
     *
     * @apiGroup group
     * @apiParam {string} token
     * @apiParam {integer} group_id 群组ID
     * @apiParam {array} user_id_arr 用户ID数组
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     *  }
     */
    public function addUser(Request $request)
    {
        // 判断是否是设计公司管理员
        if (!$this->auth_user->isDesignAdmin()) {
            return $this->response->array($this->apiError('无权限', 403));
        }

        $this->validate($request, [
            'group_id' => 'required|integer',
            'user_id_arr' => 'required|array',
        ]);

        $group_id = $request->input('group_id');
        $user_id_arr = $request->input('user_id_arr');
        $company_id = User::designCompanyId($this->auth_user_id);

        $group = Group::getGroup($group_id, $company_id);
        if ($group) {
            if ($group->addUser($user_id_arr)) {
                return $this->response->array($this->apiSuccess());
            }
        }

        return $this->response->array($this->apiError('option error', 500));
    }

    /**
     * @api {put} /group/removeUser  群组移除用户（设计公司管理员）
     * @apiVersion 1.0.0
     * @apiName group removeUser
     *
     * @apiGroup group
     * @apiParam {string} token
     * @apiParam {integer} group_id 群组ID
     * @apiParam {array} user_id_arr 用户ID数组
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     *  }
     */
    public function removeUser(Request $request)
    {
        // 判断是否是设计公司管理员
        if (!$this->auth_user->isDesignAdmin()) {
            return $this->response->array($this->apiError('无权限', 403));
        }

        $this->validate($request, [
            'group_id' => 'required|integer',
            'user_id_arr' => 'required|array',
        ]);

        $group_id = $request->input('group_id');
        $user_id_arr = $request->input('user_id_arr');
        $company_id = User::designCompanyId($this->auth_user_id);

        $group = Group::getGroup($group_id, $company_id);
        if ($group) {
            if ($group->removeUser($user_id_arr)) {
                return $this->response->array($this->apiSuccess());
            }
        }

        return $this->response->array($this->apiError('option error', 500));
    }

    /**
     * @api {delete} /group/delete  删除群组（设计公司管理员）
     * @apiVersion 1.0.0
     * @apiName group delete
     *
     * @apiGroup group
     * @apiParam {string} token
     * @apiParam {integer} group_id 群组ID
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     *  }
     */
    public function delete(Request $request)
    {
        // 判断是否是设计公司管理员
        if (!$this->auth_user->isDesignAdmin()) {
            return $this->response->array($this->apiError('无权限', 403));
        }

        $this->validate($request, [
            'group_id' => 'required|integer',
        ]);

        $group_id = $request->input('group_id');
        $company_id = User::designCompanyId($this->auth_user_id);

        $group = Group::getGroup($group_id, $company_id);
        if ($group) {
            if ($group->deleteGroup()) {
                return $this->response->array($this->apiSuccess());
            } else {
                return $this->response->array($this->apiError('群组使用中', 403));
            }
        }

        return $this->response->array($this->apiError('not found', 404));
    }


    /**
     * @api {get} /group/userGroupLists  获取某用户所在的群组列表
     * @apiVersion 1.0.0
     * @apiName group userGroupLists
     *
     * @apiGroup group
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     },
     * "data": [
     *      {
     *          "id": 1, // 群组ID
     *          "name": "测试一个群组",
     *          "type": 2   // 群组类型：1.系统创建 2. 用户创建
     *      }
     * ]
     *  }
     */
    public function userGroupLists()
    {
        // 判断是否是设计公司管理员
        if (!$this->auth_user->isDesignAdmin()) {
            return $this->response->array($this->apiError('无权限', 403));
        }

        $list = Group::userGroupList($this->auth_user_id);

        return $this->response->array($this->apiSuccess('Success', 200, $list));
    }

    /**
     * @api {get} /group/groupUserLists  获取一个群组的成员信息
     * @apiVersion 1.0.0
     * @apiName group groupUserLists
     *
     * @apiGroup group
     * @apiParam {string} token
     * @apiParam {integer} group_id 群组ID
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     * "data": [
     *      {
     *          "id": 1,    // 用户ID
     *          "username": "",  // 用户名称
     *          "logo_image": null
     *      },
     * ]
     *  }
     */
    public function groupUserLists(Request $request)
    {
        // 判断是否是设计公司管理员
        if (!$this->auth_user->isDesignAdmin()) {
            return $this->response->array($this->apiError('无权限', 403));
        }

        $this->validate($request, [
            'group_id' => 'required|integer',
        ]);

        $group_id = $request->input('group_id');
        $company_id = User::designCompanyId($this->auth_user_id);

        $group = Group::getGroup($group_id, $company_id);
        if ($group) {
            $lists = $group->userList();
            return $this->response->array($this->apiSuccess('Success', 200, $lists));
        }

        return $this->response->array($this->apiError('not found', 404));
    }

    /**
     * @api {put} /group/updateName  修改群组名称（设计公司管理员）
     * @apiVersion 1.0.0
     * @apiName group updateName
     *
     * @apiGroup group
     * @apiParam {string} token
     * @apiParam {string} name 群组名称
     * @apiParam {integer} group_id 群组ID
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     *  }
     */
    public function updateName(Request $request)
    {
        $this->validate($request, [
            'group_id' => 'required|integer',
            'name' => 'required|max:50',
        ]);

        $group_id = $request->input('group_id');
        $name = $request->input('name');
        // 判断是否是设计公司管理员
        if (!$this->auth_user->isDesignAdmin()) {
            return $this->response->array($this->apiError('无权限', 403));
        }
        $group = Group::where('id' , $group_id)->first();
        $group->name = $name;
        if($group->save()){
            return $this->response->array($this->apiSuccess());
        }

    }

}