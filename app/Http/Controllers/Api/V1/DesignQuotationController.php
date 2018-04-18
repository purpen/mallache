<?php

namespace App\Http\Controllers\Api\V1;

use App\Helper\MassageException;
use App\Http\Transformer\DesignQuotationTransformer;
use App\Models\DesignProject;
use App\Models\ItemUser;
use App\Models\ProjectPlan;
use App\Models\QuotationModel;
use App\Models\User;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DesignQuotationController extends BaseController
{
    /**
     * @api {post} /designQuotation/create 设计工具-创建报价单
     * @apiVersion 1.0.0
     * @apiName designQuotation create
     * @apiGroup designQuotation
     *
     * @apiParam {string} company_name 甲方名称
     * @apiParam {string} contact_name 甲方联系人
     * @apiParam {string} phone 联系方式
     * @apiParam {string} address 详细地址
     * @apiParam {string} design_company_name 设计公司名称
     * @apiParam {string} design_contact_name 设计联系人
     * @apiParam {string} design_phone 设计联系方式
     * @apiParam {string} design_address 设计详细地址
     * @apiParam {string} summary 项目目标
     * @apiParam {json} plan 项目计划 [            {                "content": "工作内容",                "arranged": [                    {                        "name": "结构师",                        "number": 2                    }                ],                "duration": 1,                "price": "500.00",                "summary": "备注"            }        ]
     * @apiParam {int} is_tax 是否含税
     * @apiParam {int} is_invoice 是否开发票
     * @apiParam {int} tax_rate 税率
     * @apiParam {decimal} total_price 合计金额
     * @apiParam {decimal} price 总计金额
     * @apiParam {int} design_project_id 项目管理项目ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }
     *      "data": {
     *          "company_name": "jiafang16543",
     *          "contact_name": "我是甲方liaxiren 2345",
     *          "phone": "18978787878",
     *          "address": "上海黄浦江畔",
     *          "design_company_name": null,
     *          "design_contact_name": null,
     *          "design_phone": null,
     *          "design_address": null,
     *          "project_name": "uuuu",
     *          "summary": "我门第目标是我门第目标是我门第目标是我门第目标是我门第目标是我门第目标是我门第目标是我门第目标是",
     *          "plan": [
     *              {
     *                  "content": "工作内容",
     *                  "arranged": [
     *                      {
     *                          "name": "结构师",
     *                          "number": 2
     *                      }
     *                  ],
     *                  "duration": 1,
     *                  "price": "500.00",
     *                  "summary": "备注"
     *              }
     *          ],
     *          "is_tax": "1",
     *          "is_invoice": null,
     *          "tax_rate": null,
     *          "total_price": "10000",
     *          "price": "10000"
     *      },
     *   }
     */
    public function create(Request $request)
    {
        try {
            $rules = [
                'company_name' => 'required|max:100',
                'contact_name' => 'required|max:20',
                'phone' => 'required|max:20',
                'address' => 'required|string|max:200',
                'design_company_name' => 'required|max:100',
                'design_contact_name' => 'required|max:20',
                'design_phone' => 'required|max:20',
                'design_address' => 'required|string|max:200',
                'summary' => 'max:500',
                'is_tax' => 'required|int',
                'is_invoice' => 'int',
                'tax_rate' => 'int',
                'total_price' => 'required',
                'design_project_id' => 'required|int',
                'plan' => 'string',
                'price' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                throw new StoreResourceFailedException('Error', $validator->errors());
            }

            $design_project_id = $request->input('design_project_id');
            $design_project = DesignProject::find($design_project_id);
            if (!$design_project->isPower($this->auth_user_id)) {
                throw new MassageException('无权限', 403);
            }

            $count = QuotationModel::where('design_project_id', $design_project_id)->count();
            if ($count > 0) {
                throw new MassageException('已存在，勿重复添加', 403);
            }

            DB::beginTransaction();

            // 保存报价单信息
            $quotation_info = $request->only([
                'summary',
                'is_tax',
                'is_invoice',
                'tax_rate',
                'design_project_id',
                'price',
                'total_price',
            ]);
            $quotation_info['type'] = 1; // 线下项目

            if ($quotation_info['is_tax'] == 1) { // 含税
                if ($quotation_info['price'] != $quotation_info['total_price']) {
                    throw new MassageException('合计金额和总计金额不符', 403);
                }
                $quotation_info['is_invoice'] = null;
                $quotation_info['tax_rate'] = null;
            } else if ($quotation_info['is_tax'] == 2) { // 不含税
                $a = bcmul($quotation_info['total_price'], $quotation_info['tax_rate'], 2);
                $a = bcdiv($a, 100, 2);
                if ($quotation_info['price'] != bcadd($quotation_info['total_price'], $a, 2)) {
                    throw new MassageException('合计金额和总计金额不符', 403);
                }
            }
            $quotation_info['user_id'] = $this->auth_user_id;
            $quotation_info['design_company_id'] = User::designCompanyId($this->auth_user_id);
            $quotation_info['item_demand_id'] = 0;
            $quotation = QuotationModel::create($quotation_info);


            // 保存甲方信息
            $jia_info = $request->only([
                'company_name', 'contact_name', 'phone', 'address',
                'design_company_name', 'design_contact_name', 'design_phone', 'design_address'
            ]);
            if (!$design_project) {
                throw new MassageException('not found', 404);
            }
            $jia_info['quotation_id'] = $quotation->id;
            if (!$design_project->update($jia_info)) {
                throw new MassageException('server save err', 500);
            }

            // 项目计划
            /*[
                {
                    "content": '工作内容',
                    "arranged": [
                        {
                            'name':'结构师',
                            'number':2,
                        },
                    ],
                    "duration": '持续时间',
                    "price": '费用',
                    "summary": '备注'，
                },
            ]*/

            // 项目计划
            if ($plan = $request->input('plan')) {
                $plan_arr = json_decode($plan, true);
                foreach ($plan_arr as $value) {
                    if (empty($value['content']) || empty($value['arranged']) || empty($value['duration']) || empty($value['price'])) {
                        throw new MassageException('计划内容不能为空', 403);
                    }
                    $value['quotation_id'] = $quotation->id;
                    if (!ProjectPlan::createPlan($value)) {
                        throw new MassageException('server error', 500);
                    }
                }
            }

            DB::commit();
        } catch (MassageException $e) {
            DB::rollBack();
            Log::error($e);
            return $this->response->array($this->apiError($e->getMessage(), $e->getCode()));
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $this->response->item($quotation, new DesignQuotationTransformer())->setMeta($this->apiMeta());

    }

    /**
     * @api {put} /designQuotation/update 设计工具-更新报价单
     * @apiVersion 1.0.0
     * @apiName designQuotation update
     * @apiGroup designQuotation
     *
     * @apiParam {int} id 报价单ID
     * @apiParam {string} company_name 甲方名称
     * @apiParam {string} contact_name 甲方联系人
     * @apiParam {string} phone 联系方式
     * @apiParam {string} address 详细地址
     * @apiParam {string} design_company_name 设计公司名称
     * @apiParam {string} design_contact_name 设计联系人
     * @apiParam {string} design_phone 设计联系方式
     * @apiParam {string} design_address 设计详细地址
     * @apiParam {string} summary 项目目标
     * @apiParam {json} plan 项目计划 [            {                "content": "工作内容",                "arranged": [                    {                        "name": "结构师",                        "number": 2                    }                ],                "duration": 1,                "price": "500.00",                "summary": "备注"            }        ]
     * @apiParam {int} is_tax 是否含税
     * @apiParam {int} is_invoice 是否开发票
     * @apiParam {int} tax_rate 税率
     * @apiParam {decimal} total_price 合计金额
     * @apiParam {decimal} price 总计金额
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }，
     *      "data": {
     *          "company_name": "jiafang16543",
     *          "contact_name": "我是甲方liaxiren 2345",
     *          "phone": "18978787878",
     *          "address": "上海黄浦江畔",
     *          "design_company_name": null,
     *          "design_contact_name": null,
     *          "design_phone": null,
     *          "design_address": null,
     *          "project_name": "uuuu",
     *          "summary": "我门第目标是我门第目标是我门第目标是我门第目标是我门第目标是我门第目标是我门第目标是我门第目标是",
     *          "plan": [
     *              {
     *                  "content": "工作内容",
     *                  "arranged": [
     *                      {
     *                          "name": "结构师",
     *                          "number": 2
     *                      }
     *                  ],
     *                  "duration": 1,
     *                  "price": "500.00",
     *                  "summary": "备注"
     *              }
     *          ],
     *          "is_tax": "1",
     *          "is_invoice": null,
     *          "tax_rate": null,
     *          "total_price": "10000",
     *          "price": "10000"
     *      },
     *   }
     */
    public function update(Request $request)
    {
        try {
            $rules = [
                'id' => 'required|int',
                'company_name' => 'required|max:100',
                'contact_name' => 'required|max:20',
                'phone' => 'required|max:20',
                'address' => 'required|string|max:200',
                'design_company_name' => 'required|max:100',
                'design_contact_name' => 'required|max:20',
                'design_phone' => 'required|max:20',
                'design_address' => 'required|string|max:200',
                'summary' => 'max:500',
                'is_tax' => 'required|int',
                'is_invoice' => 'int',
                'tax_rate' => 'int',
                'total_price' => 'required',
                'plan' => 'string',
                'price' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                throw new StoreResourceFailedException('Error', $validator->errors());
            }

            $id = $request->input('id');
            $quotation = QuotationModel::find($id);
            if (!$quotation) {
                throw new MassageException('not found', 404);
            }

            $design_project = $quotation->designProject;
            if (!$design_project->isPower($this->auth_user_id)) {
                throw new MassageException('无权限', 403);
            }

            DB::beginTransaction();

            // 保存甲方信息
            $jia_info = $request->only([
                'company_name', 'contact_name', 'phone', 'address',
                'design_company_name', 'design_contact_name', 'design_phone', 'design_address'
            ]);
            if (!$design_project) {
                throw new MassageException('not found', 404);
            }
            if (!$design_project->update($jia_info)) {
                throw new MassageException('server save err', 500);
            }


            // 保存报价单信息
            $quotation_info = $request->only([
                'summary',
                'is_tax',
                'is_invoice',
                'tax_rate',
                'price',
                'total_price',
            ]);

            $quotation_info['type'] = 1; // 线下项目
            if ($quotation_info['is_tax'] == 1) { // 含税
                if ($quotation_info['price'] != $quotation_info['total_price']) {
                    throw new MassageException('合计金额和总计金额不符', 403);
                }
                $quotation_info['is_invoice'] = null;
                $quotation_info['tax_rate'] = null;
            } else if ($quotation_info['is_tax'] == 2) { // 不含税
                $a = bcmul($quotation_info['total_price'], $quotation_info['tax_rate'], 2);
                $a = bcdiv($a, 100, 2);
                if ($quotation_info['price'] != bcadd($quotation_info['total_price'], $a, 2)) {
                    throw new MassageException('合计金额和总计金额不符', 403);
                }
            }
            $quotation_info['user_id'] = $this->auth_user_id;
            $quotation_info['item_demand_id'] = 0;
            $quotation->update($quotation_info);

            // 项目计划
            /*[
                {
                    "content": '工作内容',
                    "arranged": [
                        {
                            'name':'结构师',
                            'number':2,
                        },
                    ],
                    "duration": '持续时间',
                    "price": '费用',
                    "summary": '备注'，
                },
            ]*/


            //删除原报价计划表内容
            ProjectPlan::where('quotation_id', $id)->delete();
            // 项目计划
            if ($plan = $request->input('plan')) {
                $plan_arr = json_decode($plan, true);
                foreach ($plan_arr as $value) {
                    if (empty($value['content']) || empty($value['arranged']) || empty($value['duration']) || empty($value['price'])) {
                        throw new MassageException('计划内容不能为空', 403);
                    }
                    $value['quotation_id'] = $quotation->id;
                    if (!ProjectPlan::createPlan($value)) {
                        throw new MassageException('server error', 500);
                    }
                }
            }

            DB::commit();
        } catch (MassageException $e) {
            DB::rollBack();
            Log::error($e);
            return $this->response->array($this->apiError($e->getMessage(), $e->getCode()));
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $this->response->item($quotation, new DesignQuotationTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /designQuotation 设计工具-查看详情
     * @apiVersion 1.0.0
     * @apiName designQuotation show
     * @apiGroup designQuotation
     *
     * @apiParam {int} id 报价单ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }，
     *      "data": {
     *          "company_name": "jiafang16543",
     *          "contact_name": "我是甲方liaxiren 2345",
     *          "phone": "18978787878",
     *          "address": "上海黄浦江畔",
     *          "design_company_name": null,
     *          "design_contact_name": null,
     *          "design_phone": null,
     *          "design_address": null,
     *          "project_name": "uuuu",
     *          "summary": "我门第目标是我门第目标是我门第目标是我门第目标是我门第目标是我门第目标是我门第目标是我门第目标是",
     *          "plan": [
     *              {
     *                  "content": "工作内容",
     *                  "arranged": [
     *                      {
     *                          "name": "结构师",
     *                          "number": 2
     *                      }
     *                  ],
     *                  "duration": 1,
     *                  "price": "500.00",
     *                  "summary": "备注"
     *              }
     *          ],
     *          "is_tax": "1",
     *          "is_invoice": null,
     *          "tax_rate": null,
     *          "total_price": "10000",
     *          "price": "10000"
     *      },
     *   }
     */
    public function show(Request $request)
    {
        $id = $request->input('id');
        $quotation = QuotationModel::find($id);
        $design_project = $quotation->designProject;
        if (!$design_project->isPower($this->auth_user_id)) {
            throw new MassageException('无权限', 403);
        }

        return $this->response->item($quotation, new DesignQuotationTransformer())->setMeta($this->apiMeta());
    }
}
