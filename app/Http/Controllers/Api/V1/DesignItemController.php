<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\DesignItemTransformer;
use App\Models\DesignItemModel;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DesignItemController extends BaseController
{
    /**
     * @api {get} /designItem 展示服务项目类别
     * @apiVersion 1.0.0
     * @apiName designItem index
     * @apiGroup designItem
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *      "data": [
     *          {
     *              "id": 3,
     *              "user_id": 1,
     *              "design_type": "1",
     *              "project_cycle": "1",
     *              "min_price": "1.00"
     *          },
     *          {
     *              "id": 5,
     *              "user_id": 1,
     *              "design_type": "3",
     *              "project_cycle": "1",
     *              "min_price": "1.00"
     *          }
     *      ],
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *   }
     */
    public function index()
    {
        $user_id = intval($this->auth_user_id);
        $designItem = DesignItemModel::where('user_id', $user_id)->get();
        if (!$designItem) {
            return $this->response->array($this->apiSuccess());
        }
        return $this->response->collection($designItem, new DesignItemTransformer())->setMeta($this->apiMeta());
    }


    /**
     * @api {post} /designItem 保存服务项目类型
     * @apiVersion 1.0.0
     * @apiName designItem store
     * @apiGroup designItem
     *
     * @apiParam {integer} type 设计类型：1.产品设计；2.UI UX 设计 3. 平面设计  4.H5  5.包装设计  6.插画设计
     * @apiParam {integer} design_type 设计类别：产品设计（1.产品策略；2.产品外观设计；3.结构设计；）UXUI设计（1.app设计；2.网页设计；3.'界面设计', 4 . '服务设计', 5 . '用户体验咨询'）平面设计（1.'logo/VI设计', 2.'海报/宣传册', 3 .'画册/书装'）H5(1.H5) 包装设计（1.包装设计）插画（1. '商业插画', 2. '书籍插画', 3. '形象/IP插画'）
     * @apiParam {integer} project_cycle 服务项目周期 设计周期：1.1个月内；2.1-2个月；3.2个月；4.2-4个月；5.其他
     * @apiParam {string} min_price 最低价格
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": {
     *           "id": 1,
     *           "user_id": 1,
     *           "type": "1",
     *           "design_type": "1",
     *           "project_cycle": "1",
     *           "min_price": "1"
     *       },
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }
     *   }
     */
    public function store(Request $request)
    {
        $all['type'] = $request->input('type');
        $all['design_type'] = $request->input('design_type');
        $all['project_cycle'] = $request->input('project_cycle');
        $all['min_price'] = $request->input('min_price');
        $all['user_id'] = $this->auth_user_id;

        //验证规则
        $rules = [
            'type' => 'required|integer',
            'design_type' => 'required|integer',
            'project_cycle' => 'required|integer',
            'min_price' => 'required'
        ];

        $messages = [
            'type.required' => '类型不能为空',
            'design_type.required' => '设计类型不能为空',
            'roject_cycle.required' => '项目周期不能为空',
            'min_price.required' => '最低价格不能为空'
        ];
        $validator = Validator::make($all, $rules, $messages);

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }
        try {
            //根据设计类型查询是否存在
            $designItem = DesignItemModel::query()
                ->where('type', $request->input('type'))
                ->where('design_type', $request->input('design_type'))
                ->where('user_id', $this->auth_user_id)
                ->count();
            if ($designItem > 0) {
                return $this->response->array($this->apiError('已存在该类型'));
            } else {
                $designItem = DesignItemModel::create($all);
            }
        } catch (\Exception $e) {
            return $this->response->array($this->apiError());
        }
        return $this->response->item($designItem, new DesignItemTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /designItem 服务项目类型ID查看详情
     * @apiVersion 1.0.0
     * @apiName designItem show
     * @apiGroup designItem
     *
     * @apiParam {integer} id 服务项目类型ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *       {
     *           "data": {
     *              "id": 2,
     *              "user_id": 1,
     *              "design_type": "2",
     *              "project_cycle": "2",
     *              "min_price": "2.00"
     *          },
     *          "meta": {
     *              "message": "Success",
     *              "status_code": 200
     *          }
     *      }
     */
    public function show(Request $request)
    {
        $id = intval($request->input('id'));
        $designItem = DesignItemModel::where('id', $id)->first();
        if (!$designItem) {
            return $this->response->array($this->apiSuccess('没有找到该服务项目类型', 200));
        }
        return $this->response->item($designItem, new DesignItemTransformer())->setMeta($this->apiMeta());
    }


    /**
     * @api {put} /designItem/1 更新服务项目类型
     * @apiVersion 1.0.0
     * @apiName designItem update
     * @apiGroup designItem
     *
     * @apiParam {integer} type 设计类型：1.产品设计；2.UI UX 设计 3. 平面设计  4.H5  5.包装设计  6.插画设计
     * @apiParam {integer} design_type 设计类别：产品设计（1.产品策略；2.产品外观设计；3.结构设计；）UXUI设计（1.app设计；2.网页设计；3.'界面设计', 4 . '服务设计', 5 . '用户体验咨询'）平面设计（1.'logo/VI设计', 2.'海报/宣传册', 3 .'画册/书装'）H5(1.H5) 包装设计（1.包装设计）插画（1. '商业插画', 2. '书籍插画', 3. '形象/IP插画'）
     * @apiParam {integer} project_cycle 服务项目周期 设计周期：1.1个月内；2.1-2个月；3.2个月；4.2-4个月；5.其他
     * @apiParam {string} min_price 最低价格
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
    public function update(Request $request, $id)
    {
        //验证规则
        $rules = [
            'type' => 'required|integer',
            'design_type' => 'required|integer',
            'project_cycle' => 'required|integer',
            'min_price' => 'required'
        ];

        $messages = [
            'type.required' => '类型不能为空',
            'design_type.required' => '	设计类型不能为空',
            'project_cycle.required' => '项目周期不能为空',
            'min_price.required' => '最低价格不能为空'
        ];

        $validator = Validator::make($request->only(['type', 'design_type', 'project_cycle', 'min_price']), $rules, $messages);

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        $all = $request->except(['token']);
        $designItem = DesignItemModel::where('id', intval($id))->first();
        Log::info($all);
        $designItem->update($all);
        if (!$designItem) {
            return $this->response->array($this->apiError());
        }
        return $this->response->item($designItem, new DesignItemTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {delete} /designItem/{id} 根据服务项目ID删除服务项目
     * @apiVersion 1.0.0
     * @apiName designItem delete
     * @apiGroup designItem
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
     *  }
     */
    public function destroy($id)
    {
        //检验是否存在该服务项目
        $item = DesignItemModel::find($id);
        if (!$item) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        //检验是否是当前用户创建的服务项目
        if ($item->user_id != $this->auth_user_id) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        $designItem = $item->delete();
        if (!$designItem) {
            return $this->response->array($this->apiError());
        }
        return $this->response->array($this->apiSuccess());
    }
}
