<?php
namespace App\Http\Controllers\Api\Jd;

use App\Http\JdTransformer\UserTransformer;
use App\Models\User;
use Illuminate\Http\Request;

class UserJdController extends BaseController
{

    /**
     * @api {get} /jd/user/lists 获取用户信息列表
     * @apiVersion 1.0.0
     * @apiName user lists
     * @apiGroup JdUser
     *
     * @apiParam {string} token
     * @apiParam {int} page 页码 （默认：1）
     * @apiParam {int} per_page   每页数量 （默认：10）
     * @apiParam {int} sort 排序：0.正序；1.倒序；（默认：倒序）；
     * @apiParam {int} evt 查询条件：1.ID；2.手机号；3.昵称；4.邮箱；
     * @apiParam {string} val 查询值
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
    public function lists(Request $request)
    {
        $login_user_id = $this->auth_user_id;
        $source_admin = User::sourceAdmin($login_user_id);
        if($source_admin == 0){
            return $this->response->array($this->apiSuccess('登陆用户没有权限查看', 403));
        }
        $per_page = $request->input('per_page') ?? $this->per_page;
        $sort = $request->input('sort') ? (int)$request->input('sort') : 0;
        $evt = $request->input('evt') ? (int)$request->input('evt') : 1;
        $val = $request->input('val') ? $request->input('val') : '';

        $query = User::query();

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

        $lists = $query->where('type' ,1)->where('source' , $source_admin)->paginate($per_page);

        return $this->response->paginator($lists,new UserTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /jd/user/show 用户详情
     * @apiVersion 1.0.0
     * @apiName user show
     * @apiGroup JdUser
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
        $login_user_id = $this->auth_user_id;
        $source_admin = User::sourceAdmin($login_user_id);
        if($source_admin == 0){
            return $this->response->array($this->apiSuccess('登陆用户没有权限查看', 403));
        }
        $user = User::find($request->input('id'));
        if (!$user) {
            return $this->response->array($this->apiSuccess('not found', 404));
        }

        return $this->response->item($user, new UserTransformer())->setMeta($this->apiMeta());
    }

}
