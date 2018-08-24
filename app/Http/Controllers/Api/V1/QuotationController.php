<?php

namespace App\Http\Controllers\Api\V1;

use App\Helper\MassageException;
use App\Helper\Tools;
use App\Http\Transformer\DesignProjectTransformer;
use App\Http\Transformer\DesignQuotationTransformer;
use App\Http\Transformer\QuotationTransformer;
use App\Jobs\SendOneSms;
use App\Models\DesignCompanyModel;
use App\Models\DesignProject;
use App\Models\Item;
use App\Models\ItemRecommend;
use App\Models\ItemUser;
use App\Models\ProjectPlan;
use App\Models\QuotationModel;
use App\Models\User;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class QuotationController extends BaseController
{
    /**
     * @api {post} /quotation 报价单添加
     * @apiVersion 1.0.0
     * @apiName quotation store
     * @apiGroup quotation
     *
     * @apiParam {integer} item_demand_id 项目需求id
     * @apiParam {string} company_name 甲方名称
     * @apiParam {string} contact_name 甲方联系人
     * @apiParam {string} position 甲方职位
     * @apiParam {string} phone 联系方式
     * @apiParam {int} province  省份
     * @apiParam {int} city  城市
     * @apiParam {int} area 地区
     * @apiParam {string} address 详细地址
     * @apiParam {string} design_company_name 设计公司名称
     * @apiParam {string} design_contact_name 设计联系人
     * @apiParam {string} design_position 乙方职位
     * @apiParam {string} design_phone 设计联系方式
     * @apiParam {int} design_province  设计省份
     * @apiParam {int} design_city  设计城市
     * @apiParam {int} design_area 设计地区
     * @apiParam {string} design_address 设计详细地址
     * @apiParam {string} summary 项目目标
     * @apiParam {json} plan 项目计划 [            {                "content": "工作内容",                "arranged": [                    {                        "name": "结构师",                        "number": 2                    }                ],                "duration": 1,                "price": "500.00",                "summary": "备注"            }        ]
     * @apiParam {int} taxable_type 纳税类型 1. 一般纳税人 2. 小额纳税人
     * @apiParam {int} invoice_type 发票类型 1. 专票 2. 普票
     * @apiParam {decimal} total_price 合计金额
     * @apiParam {decimal} price 总计金额
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
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
     *          "item_demand_id": 1,
     *          "design_company_id": 27,
     *          "taxable_type": 1, // 纳税类型 1. 一般纳税人 2. 小额纳税人
     *          "invoice_type": 1  // 发票类型 1. 专票 2. 普票
     *          "type": 1, // 类型：0.线上 1.线下
     *      },
     *  }
     */
    public function store(Request $request)
    {

        $design = DesignCompanyModel::where('user_id', $this->auth_user_id)->first();
        if (!$design) {
            return $this->response->array($this->apiError('设计公司不存在'));
        }

        $item_demand_id = $request->input('item_demand_id');
        $item = Item::find($item_demand_id);

        try {
            $rules = [
                'item_demand_id' => 'required',
                'company_name' => 'max:100',
                'contact_name' => 'max:20',
                'phone' => 'max:20',
                'province' => 'integer',
                'city' => 'integer',
                'area' => 'integer',
                'address' => 'max:100',
                'design_company_name' => 'max:100',
                'design_contact_name' => 'max:20',
                'design_phone' => 'max:20',
                'design_province' => 'integer',
                'design_city' => 'integer',
                'design_area' => 'integer',
                'design_address' => 'max:100',
                'taxable_type' => 'integer|required|in:1,2',
                'invoice_type' => 'integer|required|in:1,2',

                'summary' => 'max:500',
                'total_price' => 'required',
                'plan' => 'string',
                'price' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                throw new StoreResourceFailedException('Error', $validator->errors());
            }

            DB::beginTransaction();

            // 系统自动创建项目管理项目
            $design_project = new DesignProject();
            $design_project->name = $item->itemInfo()['name'];
            $design_project->level = 1;  // 默认普通
            $design_project->user_id = $this->auth_user_id;
            $design_project->design_company_id = $design->id;
            $design_project->status = -1;  // 隐藏
            $design_project->project_type = 1;  // 线上
            $design_project->save();

            // 将创建者添加入项目人员
            if (!ItemUser::addItemUser($design_project->id, $this->auth_user_id, 1)) {
                throw new MassageException('项目成员添加失败', 403);
            }

            //查看项目id和设计公司id是否存在
            $item_recommend = ItemRecommend::where('item_id', $item_demand_id)->where('design_company_id', $design->id)->first();
            if ($item_recommend == null) {
                throw new MassageException('项目不合法', 403);
            }
            //查看报价单id是否为null,为null创建报价单并更新项目报价单id信息
            if ($item_recommend->quotation_id === 0) {
                // 保存报价单信息
                $quotation_info = $request->only([
                    'summary',
                    'price',
                    'total_price',
                    'item_demand_id',
                    'taxable_type',
                    'invoice_type',
                ]);
                $quotation_info['type'] = 0; // 线上项目

                // 根据纳税信息计算纳税比例
                if ($quotation_info['taxable_type'] == 1) {  // 小额纳税人 6%
                    $quotation_info['tax_rate'] = 6;
                } else {
                    // 1. 专票
                    if ($quotation_info['invoice_type'] == 1) {
                        $quotation_info['tax_rate'] = 7;
                    } else {  // 普票
                        $quotation_info['tax_rate'] = 10;
                    }
                }

                // 基础报价不含税
                $a = ($quotation_info['total_price'] * $quotation_info['tax_rate']) / 100;
                if (round($quotation_info['price'], 2) != round(((float)$quotation_info['total_price'] + $a), 2)) {
                    throw new MassageException('合计金额和总计金额不符', 403);
                }

                $quotation_info['is_tax'] = 2; // 基础价格不含税
                $quotation_info['is_invoice'] = 1; // 开票

                $quotation_info['user_id'] = $this->auth_user_id;
                $quotation_info['design_company_id'] = User::designCompanyId($this->auth_user_id);
                $quotation_info['item_demand_id'] = $item_demand_id;
                $quotation_info['design_project_id'] = $design_project->id;
                $quotation = QuotationModel::create($quotation_info);

                $item_recommend->quotation_id = $quotation->id;
                $item_recommend->design_company_status = 2;
                if ($item_recommend->save()) {
                    $item->status = 45;
                    $item->save();
                }


                // 保存甲方信息
                $jia_info = $request->only([
                    'company_name', 'contact_name', 'phone', 'address',
                    'design_company_name', 'design_contact_name', 'design_phone', 'design_address',
                    'province',
                    'city',
                    'area',
                    'design_province',
                    'design_city',
                    'design_area',
                    'design_position',
                    'position'
                ]);
                $jia_info['position'] = $request->input('position') ?? '';
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


                // 需求方通知信息
                $title = '收到报价';
                $content = '收到【' . $design->company_name . '】公司报价';
                Tools::message($item->user_id, $title, $content, 2, $item->id, $item->status);
                $message_content = '已有设计公司报价，请查阅。感谢您的信任，如有疑问欢迎致电 ';
                Tools::sendSmsToPhone($item->phone, $message_content, $item->source);

            } else {
                throw new MassageException('该项目已经报价', 403);
            }

            DB::commit();
        } catch (MassageException $e) {
            DB::rollBack();

            return $this->response->array($this->apiError($e->getMessage(), $e->getCode()));
        } catch
        (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return $this->response->item($quotation, new DesignQuotationTransformer())->setMeta($this->apiMeta());

    }

    /**
     * @api {get} /quotation/{id}  报价单ID查看详情
     * @apiVersion 1.0.0
     * @apiName quotation show
     * @apiGroup quotation
     *
     * @apiParam {integer} id 报价单ID
     * @apiParam {string} token
     * @apiSuccessExample 成功响应:
     * {
     *       "data": {
     *           "id": 3,
     *           "item_demand_id": 2,
     *           "design_company_id": 1,
     *           "price": "10000.00",
     *           "summary": "项目不错",
     *       },
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }
     *   }
     */
    public function show(Request $request)
    {
        $id = intval($request->input('id'));
        $quotation = QuotationModel::where('id', $id)->first();
        if (!$quotation) {
            return $this->response->array($this->apiSuccess());
        }
        return $this->response->item($quotation, new DesignQuotationTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {put} /quotation/1 根据报价单id更新
     * @apiVersion 1.0.0
     * @apiName quotation update
     * @apiGroup quotation
     *
     * @apiParam {string} company_name 甲方名称
     * @apiParam {string} contact_name 甲方联系人
     * @apiParam {string} position 甲方职位
     * @apiParam {string} phone 联系方式
     * @apiParam {int} province  省份
     * @apiParam {int} city  城市
     * @apiParam {int} area 地区
     * @apiParam {string} address 详细地址
     * @apiParam {string} design_company_name 设计公司名称
     * @apiParam {string} design_contact_name 设计联系人
     * @apiParam {string} design_position 乙方职位
     * @apiParam {string} design_phone 设计联系方式
     * @apiParam {int} design_province  设计省份
     * @apiParam {int} design_city  设计城市
     * @apiParam {int} design_area 设计地区
     * @apiParam {string} design_address 设计详细地址
     * @apiParam {string} summary 项目目标
     * @apiParam {json} plan 项目计划 [            {                "content": "工作内容",                "arranged": [                    {                        "name": "结构师",                        "number": 2                    }                ],                "duration": 1,                "price": "500.00",                "summary": "备注"            }        ]
     * @apiParam {int} taxable_type 纳税类型 1. 一般纳税人 2. 小额纳税人
     * @apiParam {int} invoice_type 发票类型 1. 专票 2. 普票
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
     *          "price": "10000",
     *          "taxable_type": 1, // 纳税类型 1. 一般纳税人 2. 小额纳税人
     *          "invoice_type": 1  // 发票类型 1. 专票 2. 普票
     *          "type": 1, // 类型：0.线上 1.线下
     *      },
     *   }
     */
    public function update(Request $request, $id)
    {
        try {
            $rules = [
                'company_name' => 'max:100',
                'contact_name' => 'max:20',
                'phone' => 'max:20',
                'address' => 'max:100',
                'design_company_name' => 'max:100',
                'design_contact_name' => 'max:20',
                'design_phone' => 'max:20',
                'design_address' => 'max:100',
                'summary' => 'max:500',
                'taxable_type' => 'integer|required|in:1,2',
                'invoice_type' => 'integer|required|in:1,2',
                'total_price' => 'required',
                'plan' => 'string',
                'price' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                throw new StoreResourceFailedException('Error', $validator->errors());
            }

            $quotation = QuotationModel::find($id);
            if (!$quotation) {
                throw new MassageException('not found1', 404);
            }

            if ($quotation->user_id != $this->auth_user_id) {
                throw new MassageException('not found2', 404);
            }

            //如果已经确认了，就不能更新报价单信息了
            if ($quotation->status == 1) {
                throw new MassageException('该项目已确认,不能修改', 403);
            }

            $design_project = $quotation->designProject;

            DB::beginTransaction();

            // 保存甲方信息
            $jia_info = $request->only([
                'company_name',
                'contact_name',
                'phone',
                'address',
                'design_company_name',
                'design_contact_name',
                'design_phone',
                'design_address',
                'province',
                'city',
                'area',
                'design_province',
                'design_city',
                'design_area',
                'design_position',
                'position'
            ]);
            $jia_info['position'] = $request->input('position') ?? '';

            if (!$item = Item::find($quotation->item_demand_id)) {
                throw new MassageException('not found3', 404);
            }
            if (!$design_project) {  // 兼容报价单历史数据

                // 系统自动创建项目管理项目
                $design_project = new DesignProject();
                $design_project->name = $item->itemInfo()['name'];
                $design_project->level = 1;  // 默认普通
                $design_project->user_id = $this->auth_user_id;
                $design_project->design_company_id = $quotation->design_company_id;
                $design_project->status = -1;  // 隐藏
                $design_project->project_type = 1;  // 线上
                $design_project->save();

                // 将创建者添加入项目人员
                if (!ItemUser::addItemUser($design_project->id, $this->auth_user_id, 1)) {
                    throw new MassageException('项目成员添加失败', 403);
                }

            }
            $jia_info = array_diff($jia_info, ['', ' ', null]);
            if (!$design_project->update($jia_info)) {
                throw new MassageException('server save err', 500);
            }


            // 保存报价单信息
            $quotation_info = $request->only([
                'summary',
                'taxable_type',
                'invoice_type',
                'price',
                'total_price',
            ]);

            $quotation_info['type'] = 0; // 线上项目

            // 根据纳税信息计算纳税比例
            if ($quotation_info['taxable_type'] == 1) {  // 小额纳税人 6%
                $quotation_info['tax_rate'] = 6;
            } else {
                // 1. 专票
                if ($quotation_info['invoice_type'] == 1) {
                    $quotation_info['tax_rate'] = 7;
                } else {  // 普票
                    $quotation_info['tax_rate'] = 10;
                }
            }

            // 基础报价不含税
            $a = ($quotation_info['total_price'] * $quotation_info['tax_rate']) / 100;
            if (round($quotation_info['price'], 2) != round(((float)$quotation_info['total_price'] + $a), 2)) {
                throw new MassageException('合计金额和总计金额不符', 403);
            }

            $quotation_info['is_tax'] = 2; // 基础价格不含税
            $quotation_info['is_invoice'] = 1; // 开票
            $quotation_info['user_id'] = $this->auth_user_id;
            $quotation_info['design_project_id'] = $design_project->id;
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

            $design = $quotation->designCompany;
            // 需求方通知信息
            $title = '收到新报价';
            $content = '收到【' . $design->company_name . '】公司新报价';
            Tools::message($item->user_id, $title, $content, 2, $item->id, $item->status);
            Tools::sendSmsToPhone($item->phone, $content, $item->source);

            DB::commit();
        } catch (MassageException $e) {
            DB::rollBack();
            Log::error($e);
            return $this->response->array($this->apiError($e->getMessage(), $e->getCode()));
        }

        return $this->response->item($quotation, new DesignQuotationTransformer())->setMeta($this->apiMeta());
    }

}
