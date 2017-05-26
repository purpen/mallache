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
     * @apiParam {int} type 1.需求公司；2.设计公司;
     * @apiParam {int} role_id 角色：0.用户；1.管理员；
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
     *          "role_id": 1    // 角色：0.用户；1.管理员；
     *          "type": 1  //1.需求公司；2.设计公司
    }
     *   }
     */
    public function lists(\Illuminate\Http\Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        $status = in_array($request->input('status'), [0,-1]) ? $request->input('status') : null;
        $role_id = in_array($request->input('role_id'), [0,1]) ? $request->input('role_id') : null;
        $type = in_array($request->input('type'), [1,2]) ? $request->input('type') : null;

        $user = User::query();

        if($status !== null){
            $user->where('status', $status);
        }
        if($role_id !== null){
            $user->where('role_id', $role_id);
        }
        if($type !== null){
            $user->where('type', $type);
        }
        if($request->input('sort') === 0){
            $sort = 'asc';
        }else{
            $sort = 'desc';
        }

        $lists = $user->orderBy('id', $sort)->paginate($per_page);

        return $this->response->paginator($lists,new UserTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {post} /admin/user/changeRole 修改用户角色
     * @apiVersion 1.0.0
     * @apiName user changeRole
     * @apiGroup AdminUser
     *
     * @apiParam {integer} user_id 用户ID
     * @apiParam {int} role_id 角色：0.用户；1.管理员；
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
            'role_id' => ['required', Rule::in([0, 1])],
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