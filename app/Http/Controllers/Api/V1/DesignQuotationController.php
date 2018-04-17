<?php

namespace App\Http\Controllers\Api\V1;

use App\Helper\MassageException;
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
    public function create(Request $request)
    {
        try {
            $rules = [
                'company_name' => 'required|max:100',
                'contact_name' => 'required|max:20',
                'phone' => 'required|max:20',
                'address' => 'required|string|max:200',
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
            if (!ItemUser::checkUser($design_project_id, $this->auth_user_id)) {
                throw new MassageException('无权限', 403);
            }

            DB::beginTransaction();

            // 保存甲方信息
            $jia_info = $request->only(['company_name', 'contact_name', 'phone', 'address']);
            $design_project = DesignProject::find($design_project_id);
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

            // 项目计划
//            [
//                {
//                    "content": '工作内容',
//                    "arranged": [
//                        {
//                            'name':'结构师',
//                            'number':2,
//                        },
//                    ],
//                    "duration": '持续时间',
//                    "price": '费用',
//                    "summary": '备注'，
//                },
//            ]

            // 项目计划
            if ($plan = $request->plan) {
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


        } catch (MassageException $e) {
            DB::rollBack();
            Log::error($e);
            return $this->response->array($this->apiError($e->getMessage(), $e->getCode()));
        }

        return $this->response->item()->setMeta($this->apiMeta());

    }

}
