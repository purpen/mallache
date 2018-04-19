<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\DesignPositionTransformer;
use App\Models\DesignPosition;
use App\Models\User;
use Illuminate\Http\Request;

class DesignPositionController extends BaseController
{
    /**
     * @api {post} /designPosition/create 添加职位
     * @apiVersion 1.0.0
     * @apiName designPosition create
     * @apiGroup designPosition
     *
     * @apiParam {string} name // 名称
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }
     *   }
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
        ]);

        $design_company_id = User::designCompanyId($this->auth_user_id);
        $name = $request->input('name');

        $count = DesignPosition::where('name', $name)->count();
        if ($count > 0) {
            return $this->response->array($this->apiError('已存在', 403));
        }

        $design_position = new DesignPosition();
        $design_position->name = $name;
        $design_position->design_company_id = $design_company_id;
        $design_position->save();

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {get} /designPosition/lists 职位列表
     * @apiVersion 1.0.0
     * @apiName designPosition lists
     * @apiGroup designPosition
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": [{
     *          "id": 2,
     *          "name": , // 职位名称
     *       },],
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }
     *   }
     */
    public function lists()
    {
        $design_company_id = User::designCompanyId($this->auth_user_id);
        $lists = DesignPosition::where('design_company_id', $design_company_id)->get();

        return $this->response->collection($lists, new DesignPositionTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /designPosition/search 职位搜索
     * @apiVersion 1.0.0
     * @apiName designPosition search
     * @apiGroup designPosition
     *
     * @apiParam {string} name // 名称
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": [{
     *          "id": 2,
     *          "name": , // 职位名称
     *       },],
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }
     *   }
     */
    public function search(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
        ]);

        $name = $request->input('name');
        $design_company_id = User::designCompanyId($this->auth_user_id);
        $design_positions = DesignPosition::where('design_company_id', $design_company_id)
            ->where('name', 'like', "%$name%")
            ->get();

        return $this->response->collection($design_positions, new DesignPositionTransformer())->setMeta($this->apiMeta());
    }
}