<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\AdminTransformer\UserTransformer;
use App\Models\User;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserActionController extends BaseController
{

    /**
     * @api {get} /admin/user/lists 获取用户信息列表
     * @apiVersion 1.0.0
     * @apiName user lists
     * @apiGroup AdminUser
     *
     * @apiParam {string} token
     * @apiParam {int} page 页码 （默认：1）
     * @apiParam {int} per_page   每页数量 （默认：10）
     * @apiParam {int} sort 排序：0.正序；1.倒序；（默认：倒序）；
     * @apiParam {int} status 状态：；-1：禁用；0.激活;
     * @apiParam {int} type 0.全部；1.需求公司；2.设计公司;
     * @apiParam {int} evt 查询条件：1.ID；2.手机号；3.昵称；4.邮箱；
     * @apiParam {string} val 查询值
     * @apiParam {int} role_id  角色：0.用户; 10.管理员admin； 15:管理员admin_plus  20：超级管理员root
     *
     * @apiSuccessExample 成功响应:
     * {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     *      "data": {
     *          "id": 1,
     *          "account": "18629493221",
     *          "username": "",
     *          "email": null,
     *          "phone": "18629493221",
     *          "status": 0, //状态：；-1：禁用；0.激活;
     *          "item_sum": 0, //项目数量
     *          "price_total": "0.00", //总金额
     *          "price_frozen": "0.00", //冻结金额
     *          "image": "",
     *          "design_company_id": 1,
     *          "role_id": 1    // role_id 角色：0.用户; 10.管理员admin； 15:管理员admin_plus  20：超级管理员root
     *          "type": 1  //1.需求公司；2.设计公司
     *          "realname": "马哲",   // 真实姓名
     *          "position": "所属职位", // 程序员
     *          "kind": 1,  用户类型：1.普通；2.员工；3.--
    }
     *   }
     */
    public function lists(\Illuminate\Http\Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        $status = in_array($request->input('status'), [0,-1]) ? $request->input('status') : null;
        $role_id = $request->input('role_id') ? $request->input('role_id') : null;
        $type = $request->input('type') ? (int)$request->input('type') : 0;
        $sort = $request->input('sort') ? (int)$request->input('sort') : 0;
        $evt = $request->input('evt') ? (int)$request->input('evt') : 1;
        $val = $request->input('val') ? $request->input('val') : '';

        $query = User::query();

        if($status !== null){
            $query->where('status', (int)$status);
        }
        if($role_id !== null){
            $query->where('role_id', (int)$role_id);
        }
        if($type !==0 ){
            $query->where('type', (int)$type);
        }

        if ($val) {
            switch($evt) {
                case 1:
                    $query->where('id', (int)$val);
                    break;
                case 2:
                    $query->where('account', $val);
                    break;
                case 3:
                    $query->where('username', $val);
                    break;
                case 4:
                    $query->where('email', $val);
                    break;
                default:
                    $query->where('id', (int)$val);
            }
        }

        //排序
        switch ($sort){
            case 0:
                $query->orderBy('id', 'desc');
                break;
            case 1:
                $query->orderBy('id', 'asc');
                break;
        }

        $lists = $query->paginate($per_page);

        return $this->response->paginator($lists,new UserTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /admin/user/show 用户详情
     * @apiVersion 1.0.0
     * @apiName user show
     * @apiGroup AdminUser
     *
     * @apiParam {string} id
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     * "data": {
     *          "id": 1,
     *          "account": "18629493221",
     *          "username": "",
     *          "email": null,
     *          "phone": "18629493221",
     *          "status": 0, //状态：；-1：禁用；0.激活;
     *          "item_sum": 0, //项目数量
     *          "price_total": "0.00", //总金额
     *          "price_frozen": "0.00", //冻结金额
     *          "image": "",
     *          "design_company_id": 1,
     *          "role_id": 1    // role_id 角色：0.用户; 10.管理员admin； 15:管理员admin_plus  20：超级管理员root
     *          "type": 1  //1.需求公司；2.设计公司
     *          "realname": "马哲",   // 真实姓名
     *          "position": "所属职位", // 程序员
     *          "kind": 1,  用户类型：1.普通；2.员工；3.--
     * }
     * }
     */
    public function show(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ]);

        $user = User::find($request->input('id'));
        if (!$user) {
            return $this->response->array($this->apiSuccess('not found', 404));
        }

        return $this->response->item($user, new UserTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {post} /admin/user/changeRole 修改用户角色
     * @apiVersion 1.0.0
     * @apiName user changeRole
     * @apiGroup AdminUser
     *
     * @apiParam {integer} user_id 用户ID
     * @apiParam {int} role_id 角色：0.用户; 10.管理员admin； 15:管理员admin_plus  20：超级管理员root
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     *   }
     */
    public function changeRole(Request $request)
    {
        // 验证规则
        $rules = [
            'role_id' => ['required', 'integer'],
            'user_id' => ['required', 'integer'],
        ];
        $payload = $request->only('role_id', 'user_id');
        $validator = Validator::make($payload, $rules);
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        if(!$user = User::find($payload['user_id'])){
            return $this->response->array($this->apiError('not found', 404));
        }
        $user->role_id = $payload['role_id'];
        if(!$user->save()){
            return $this->response->array($this->apiError());
        }else{
            return $this->response->array($this->apiSuccess());
        }
    }

    /**
     * @api {post} /admin/user/edit 修改用户基本信息
     * @apiVersion 1.0.0
     * @apiName user edit
     * @apiGroup AdminUser
     *
     * @apiParam {integer} id 用户ID
     * @apiParam {integer} kind: 1.默认；2.员工；3.--； 
     * @apiParam {string} realname  真实姓名
     * @apiParam {string} position  职位
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     *   }
     */
    public function edit(Request $request)
    {
        $id = $request->input('id') ? (int)$request->input('id') : 0;

        if (empty($id)) {
            return $this->response->array($this->apiError('用户ID不存在！', 500));
        }

        // 验证规则
        $rules = [
            'kind' => ['required', 'integer'],
        ];
        $params = $request->only('realname', 'position', 'kind');
        $validator = Validator::make($params, $rules);
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        if(!$user = User::find($id)){
            return $this->response->array($this->apiError('not found', 404));
        }
        foreach ($params as $k=>$v) {
            if ($v) $user->$k = $v;
        }
        if(!$user->update()){
            return $this->response->array($this->apiError('保存失败！', 500));
        }else{
            return $this->response->array($this->apiSuccess());
        }
    }

    /**
     * @api {post} /admin/user/changeStatus 修改用户状态
     * @apiVersion 1.0.0
     * @apiName user changeStatus
     * @apiGroup AdminUser
     *
     * @apiParam {integer} user_id 用户ID
     * @apiParam {integer} status 状态：-1.禁用；0.正常；
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     *   }
     */
    public function changeStatus(Request $request)
    {
        $role_id = $request->input('stats');

        // 验证规则
        $rules = [
            'status' => ['required', Rule::in([-1, 0])],
            'user_id' => ['required', 'integer'],
        ];
        $payload = $request->only('status', 'user_id');
        $validator = Validator::make($payload, $rules);
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        if(!$user = User::find($payload['user_id'])){
            return $this->response->array($this->apiError('not found', 404));
        }
        $user->status = $payload['status'];
        if(!$user->save()){
            return $this->response->array($this->apiError());
        }else{
            return $this->response->array($this->apiSuccess());
        }
    }

}
