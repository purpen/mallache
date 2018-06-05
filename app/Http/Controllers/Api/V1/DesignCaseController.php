<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\DesignCaseListsTransformer;
use App\Http\Transformer\DesignCaseOpenTransformer;
use App\Http\Transformer\DesignCaseTransformer;
use App\Models\AssetModel;
use App\Models\DesignCaseModel;
use App\Models\DesignCompanyModel;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DesignCaseController extends BaseController
{
    /**
     * @api {get} /designCase  用户id查看设计公司案例展示
     * @apiVersion 1.0.0
     * @apiName designCase index
     * @apiGroup designCase
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     *   {
     *    "data": {
     *      "id": 23,
     *      "prize": 1,
     *      "prize_val": "德国红点设计奖",
     *      "title": "1",
     *      "prize_time": "1991-01-20",
     *      "sales_volume": 1,
     *      "sales_volume_val": "100-500w",
     *      "customer": "1",
     *      "field": 2,
     *      "field_val": "消费电子",
     *      "profile": "1",
     *      "status": 0,
     *      "case_image": [],
     *      "industry": 2,
     *      "industry_val": "消费零售",
     *      "type": 1,
     *      "type_val": "产品设计",
     *      "design_type": 1,
     *      "design_type_val": "产品策略",
     *      "other_prize": "",
     *      "mass_production": 1,
     *      "cover": "",
     *      "cover_id": 2,
     *      },
     *      "meta": {
     *      "message": "Success",
     *      "status_code": 200
     *      }
     *   }
     *
     */
    public function index()
    {
        $user_id = intval($this->auth_user_id);
        $designCase = DesignCaseModel::query()
            ->with('DesignCompany')
            ->where('user_id', $user_id)->get();

        return $this->response->collection($designCase, new DesignCaseTransformer())->setMeta($this->apiMeta());

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * @api {post} /designCase 保存设计公司案例
     * @apiVersion 1.0.0
     * @apiName designCase store
     * @apiGroup designCase
     * @apiParam {string} title 标题
     * @apiParam {integer} prize 奖项:1.德国红点设计奖;2.德国IF设计奖;3.IDEA工业设计奖;4.中国红星奖;5.中国红棉奖;6.台湾金点奖;7.香港DFA设计奖 ;8.日本G-Mark设计奖;9.韩国好设计奖;10.新加坡设计奖;11.意大利—Compasso d`Oro设计奖;12.英国设计奖;20:其他 (字段停用)
     * @apiParam {string} prize_time 获奖时间 （字段停用）
     * @apiParam {integer} mass_production 是否量产:0.否；1.是；
     * @apiParam {integer} sales_volume 销售金额:1.100-500w;2.500-1000w;3.1000-5000w;4.5000-10000w;5.10000w以上
     * @apiParam {string} customer 服务客户
     * @apiParam {string} profile   功能描述
     * @apiParam {integer} type   设计类型：1.产品设计；2.UI UX 设计；
     * @apiParam {integer} design_type   设计类别：产品设计（1.产品策略；2.产品设计；3.结构设计；）UXUI设计（1.app设计；2.网页设计；）
     * @apiParam {integer} field 所属领域 1.智能硬件;2.消费电子;3.交通工具;4.工业设备;5.厨电厨具;6.医疗设备;7.家具用品;8.办公用品;9.大家电;10.小家电;11.卫浴;12.玩具;13.体育用品;14.军工设备;15.户外用品
     * @apiParam {integer} industry 所属行业 1.制造业;2.消费零售;3.信息技术;4.能源;5.金融地产;6.服务业;7.医疗保健;8.原材料;9.工业制品;10.军工;11.公用事业
     * @apiParam {string} other_prize   其他奖项
     * @apiParam {integer} cover_id 封面图ID
     * @apiParam {json} patent 获得专利 1.发明专利；2.实用新型专利；3.外观设计专利 [{'time':2018-1-1,'type': 1}]
     * @apiParam {json} prizes 奖项:1.德国红点设计奖;2.德国IF设计奖;3.IDEA工业设计奖;4.中国红星奖;5.中国红棉奖;6.台湾金点奖;7.香港DFA设计奖 ;8.日本G-Mark设计奖;9.韩国好设计奖;10.新加坡设计奖;11.意大利—Compasso dOro设计奖;12.英国设计奖;13.中国优秀工业设计奖；14.DIA中国设计智造大奖；15.中国好设计奖；16.澳大利亚国际设计奖;20:其他 [{'time':2018-1-1,'type': 1}]
     * @apiParam {array} label 标签
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *    "data": {
     *      "id": 23,
     *      "prize": 1,
     *      "prize_val": "德国红点设计奖",
     *      "title": "1",
     *      "prize_time": "1991-01-20",
     *      "sales_volume": 1,
     *      "sales_volume_val": "100-500w",
     *      "customer": "1",
     *      "field": 2,
     *      "field_val": "消费电子",
     *      "profile": "1",
     *      "status": 0,
     *      "case_image": [],
     *      "industry": 2,
     *      "industry_val": "消费零售",
     *      "type": 1,
     *      "type_val": "产品设计",
     *      "design_type": 1,
     *      "design_type_val": "产品策略",
     *      "other_prize": "",
     *      "mass_production": 1,
     *      "cover": "",
     *      "cover_id": 2,
     *      "patent": [{'time':2018-1-1,'type': 1}],
     *      "prizes": [{'time':2018-1-1,'type': 1}],
     *      },
     *      "meta": {
     *      "message": "Success",
     *      "status_code": 200
     *      }
     *  }
     */
    public function store(Request $request)
    {
        $design = DesignCompanyModel::where('user_id', $this->auth_user_id)->first();
        // 验证规则
        $rules = [
            'title' => 'required|max:50',
            'mass_production' => 'required|integer',
            'customer' => 'required|max:50',
            'profile' => 'required|max:500',
            'field' => 'nullable|integer',
            'type' => 'integer',
            'design_type' => 'integer',
            'industry' => 'nullable|integer',
            'prize_time' => 'nullable|date',
            'prize' => 'nullable|integer',
            'sales_volume' => 'nullable|integer',
            'cover_id' => 'required|integer',
            'label' => 'array'
        ];
        $messages = [
            'title.required' => '标题不能为空',
            'title.max' => '最多50字符',
            'mass_production.required' => '是否量产不能为空',
            'customer.required' => '服务客户不能为空',
            'customer.max' => '最多50字符',
            'profile.required' => '项目描述不能为空',
            'profile.max' => '最多500字符',
            'field.integer' => '所属领域必须为整形',
            'type.integer' => '设计类型必须为整形',
            'design_type.integer' => '设计类别必须为整形',
            'industry.integer' => '所属行业必须为整形',
            'prize_time.date' => '日期格式不正确',
        ];
        $all['title'] = $request->input('title');
        $all['prize'] = $request->input('prize') ?? 0;
        if ($all['prize'] == 20) {
            $all['other_prize'] = $request->input('other_prize');
        }
        $all['prize_time'] = $request->input('prize_time');
        $all['sales_volume'] = $request->input('sales_volume') ?? 0;
        $all['mass_production'] = $request->input('mass_production') ?? 0;
        $all['customer'] = $request->input('customer');
        $all['field'] = $request->input('field') ?? 0;
        $all['profile'] = $request->input('profile');
        $all['user_id'] = $this->auth_user_id;
        $all['type'] = $request->input('type', 0);
        $all['design_type'] = $request->input('design_type', 0);
        $all['industry'] = $request->input('industry') ?? 0;
        $all['status'] = 1;
        $all['design_company_id'] = $design->id;
        $all['cover_id'] = $request->input("cover_id");
        $all['patent'] = $request->input('patent') ?? '[]';
        $all['prizes'] = $request->input('prizes') ?? '[]';

        if (!$this->isPrizes($all['prizes'])) {
            return $this->response->array($this->apiError('获得奖项数据不正确', 403));
        }
        if (!$this->isPrizes($all['patent'])) {
            return $this->response->array($this->apiError('专利数据不正确', 403));
        }

        $validator = Validator::make($all, $rules, $messages);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        // 验证标签
        $label = $request->input('label');
        if (is_array($label)) {
            $label = implode(',', $label);
            $all['label'] = $label;
        }


        try {
            $designCase = DesignCaseModel::create($all);
            $random = $request->input('random') ?? '';
            AssetModel::setRandom($designCase->id, $random);
        } catch (\Exception $e) {

            throw new HttpException($e->getMessage(), $e->getCode());
        }

        return $this->response->item($designCase, new DesignCaseTransformer())->setMeta($this->apiMeta());
    }

    // 判断获得奖项数据格式是否正确
    protected function isPrizes($value)
    {
        $data = json_decode($value, true);
        if (empty($data) || (isset($data[0]) && $data[0]['time'] && $data[0]['type'])) {
            return true;
        }

        return false;
    }

    /**
     * @api {get} /designCase/{case_id}  公司案例ID查看详情
     * @apiVersion 1.0.0
     * @apiName designCase show
     * @apiGroup designCase
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *    "data": {
     *      "id": 23,
     *      "prize": 1,
     *      "prize_val": "德国红点设计奖",    // 停用
     *      "title": "1",
     *      "prize_time": "1991-01-20",     // 停用
     *      "sales_volume": 1,
     *      "sales_volume_val": "100-500w",
     *      "customer": "1",
     *      "field": 2,
     *      "field_val": "消费电子",
     *      "profile": "1",
     *      "status": 0,
     *      "case_image": [],
     *      "industry": 2,
     *      "industry_val": "消费零售",
     *      "type": 1,
     *      "type_val": "产品设计",
     *      "design_type": 1,
     *      "design_type_val": "产品策略",
     *      "other_prize": "",
     *      "mass_production": 1,
     *      "cover": "",
     *      "cover_id": 2,
     *      "patent": [{'time':2018-1-1,'type': 1}], // 获得专利 1.发明专利；2.实用新型专利；3.外观设计专利
     *      "prizes": [{'time':2018-1-1,'type': 1}], // 奖项:1.德国红点设计奖;2.德国IF设计奖;3.IDEA工业设计奖;4.中国红星奖;5.中国红棉奖;6.台湾金点奖;7.香港DFA设计奖 ;8.日本G-Mark设计奖;9.韩国好设计奖;10.新加坡设计奖;11.意大利—Compasso dOro设计奖;12.英国设计奖;13.中国优秀工业设计奖；14.DIA中国设计智造大奖；15.中国好设计奖；16.澳大利亚国际设计奖;20:其他
     *      },
     *      "meta": {
     *      "message": "Success",
     *      "status_code": 200
     *      }
     *   }
     */
    public function show($case_id)
    {
        $case_id = intval($case_id);
        $designCase = DesignCaseModel::find($case_id);

        //判断是否有有权限查看案例详情
        $design_company = new DesignCompanyModel();
        // 此参数用来判断是否返回设计公司的联系方式
        $is_phone = true;
        if (($this->auth_user_id == null) || !$design_company->isRead($this->auth_user_id, $designCase->design_company_id)) {
            $is_phone = false;
        }

        if (!$designCase) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if ($is_phone) {
            return $this->response->item($designCase, new DesignCaseTransformer())->setMeta($this->apiMeta());
        } else {
            return $this->response->item($designCase, new DesignCaseOpenTransformer())->setMeta($this->apiMeta());
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DesignCaseModel $designCaseModel
     * @return \Illuminate\Http\Response
     */
    public function edit(DesignCaseModel $designCaseModel)
    {
        //
    }

    /**
     * @api {put} /designCase/12 根据公司案例ID更新案例数据
     * @apiVersion 1.0.0
     * @apiName designCase update
     * @apiGroup designCase
     * @apiParam {string} title 标题
     * @apiParam {integer} prize 奖项:1.德国红点设计奖;2.德国IF设计奖;3.IDEA工业设计奖;4.中国红星奖;5.中国红棉奖;6.台湾金点奖;7.香港DFA设计奖 ;8.日本G-Mark设计奖;9.韩国好设计奖;10.新加坡设计奖;11.意大利—Compasso d`Oro设计奖;12.英国设计奖;20:其他 (停用)
     * @apiParam {string} prize_time 获奖时间 （停用）
     * @apiParam {integer} mass_production 是否量产:0.否；1.是；
     * @apiParam {integer} sales_volume 销售金额:1.100-500w;2.500-1000w;3.1000-5000w;4.5000-10000w;5.10000w以上
     * @apiParam {string} customer 服务客户
     * @apiParam {string} profile   功能描述
     * @apiParam {integer} type   设计类型：1.产品设计；2.UI UX 设计；
     * @apiParam {integer} design_type   设计类别：产品设计（1.产品策略；2.产品设计；3.结构设计；）UXUI设计（1.app设计；2.网页设计；）
     * @apiParam {integer} field 所属领域 1.智能硬件;2.消费电子;3.交通工具;4.工业设备;5.厨电厨具;6.医疗设备;7.家具用品;8.办公用品;9.大家电;10.小家电;11.卫浴;12.玩具;13.体育用品;14.军工设备;15.户外用品
     * @apiParam {integer} industry 所属行业 1.制造业;2.消费零售;3.信息技术;4.能源;5.金融地产;6.服务业;7.医疗保健;8.原材料;9.工业制品;10.军工;11.公用事业
     * @apiParam {string} other_prize   其他奖项
     * @apiParam {json} patent 获得专利 1.发明专利；2.实用新型专利；3.外观设计专利 [{'time':2018-1-1,'type': 1}]
     * @apiParam {json} prizes 奖项:1.德国红点设计奖;2.德国IF设计奖;3.IDEA工业设计奖;4.中国红星奖;5.中国红棉奖;6.台湾金点奖;7.香港DFA设计奖 ;8.日本G-Mark设计奖;9.韩国好设计奖;10.新加坡设计奖;11.意大利—Compasso dOro设计奖;12.英国设计奖;13.中国优秀工业设计奖；14.DIA中国设计智造大奖；15.中国好设计奖；16.澳大利亚国际设计奖;20:其他 [{'time':2018-1-1,'type': 1}]
     * @apiParam {array} label 标签
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *  {
     *    "data": {
     *      "id": 23,
     *      "prize": 1,
     *      "prize_val": "德国红点设计奖",
     *      "title": "1",
     *      "prize_time": "1991-01-20",
     *      "sales_volume": 1,
     *      "sales_volume_val": "100-500w",
     *      "customer": "1",
     *      "field": 2,
     *      "field_val": "消费电子",
     *      "profile": "1",
     *      "status": 0,
     *      "case_image": [],
     *      "industry": 2,
     *      "industry_val": "消费零售",
     *      "type": 1,
     *      "type_val": "产品设计",
     *      "design_type": 1,
     *      "design_type_val": "产品策略",
     *      "other_prize": "",
     *      "mass_production": 1,
     *      "cover": "",
     *      "cover_id": 2,
     *      "patent": [{'time':2018-1-1,'type': 1}], // 获得专利 1.发明专利；2.实用新型专利；3.外观设计专利
     *      "prizes": [{'time':2018-1-1,'type': 1}], // 奖项:1.德国红点设计奖;2.德国IF设计奖;3.IDEA工业设计奖;4.中国红星奖;5.中国红棉奖;6.台湾金点奖;7.香港DFA设计奖 ;8.日本G-Mark设计奖;9.韩国好设计奖;10.新加坡设计奖;11.意大利—Compasso dOro设计奖;12.英国设计奖;13.中国优秀工业设计奖；14.DIA中国设计智造大奖；15.中国好设计奖；16.澳大利亚国际设计奖;20:其他
     *      },
     *      "meta": {
     *      "message": "Success",
     *      "status_code": 200
     *      }
     *   }
     */
    public function update(Request $request, $id)
    {
        // 验证规则
        $rules = [
            'title' => 'required|max:50',
            'mass_production' => 'required|integer',
            'customer' => 'required|max:50',
            'profile' => 'required|max:500',
            'field' => 'integer',
            'type' => 'integer',
            'design_type' => 'integer',
            'industry' => 'integer',
            'prize_time' => 'nullable|date',
            'prize' => 'nullable|integer',
            'sales_volume' => 'nullable|integer',
            'cover_id' => 'nullable|integer',
            'label' => 'array',
        ];
        $messages = [
            'title.required' => '标题不能为空',
            'title.max' => '最多50字符',
            'mass_production.required' => '是否量产不能为空',
            'customer.required' => '服务客户不能为空',
            'customer.max' => '最多50字符',
            'profile.required' => '项目描述不能为空',
            'profile.max' => '最多500字符',
            'field.integer' => '所属领域必须为整形',
            'type.integer' => '设计类型必须为整形',
            'design_type.integer' => '设计类别必须为整形',
            'industry.integer' => '所属行业必须为整形',
        ];
        $validator = Validator::make($request->only(['type', 'design_type', 'industry', 'title', 'mass_production', 'customer', 'field', 'profile']), $rules, $messages);

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        $all['patent'] = $request->input('patent') ?? '[]';
        $all['prizes'] = $request->input('prizes') ?? '[]';

        if (!$this->isPrizes($all['prizes'])) {
            return $this->response->array($this->apiError('获得奖项数据不正确', 403));
        }
        if (!$this->isPrizes($all['patent'])) {
            return $this->response->array($this->apiError('专利数据不正确', 403));
        }

        $all = $request->except(['token', 'status']);

        //检验是否存在该案例
        $case = DesignCaseModel::find($id);
        if (!$case) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        //检验是否是当前用户创建的案例
        if ($case->user_id != $this->auth_user_id) {
            return $this->response->array($this->apiError('not found!', 404));
        }

        // 验证标签
        $label = $request->input('label');
        if (is_array($label)) {
            $label = implode(',', $label);
            $all['label'] = $label;
        }

        $designCase = DesignCaseModel::where('id', intval($id))->first();
        $designCase->update($all);
        if (!$designCase) {
            return $this->response->array($this->apiError());
        }
        return $this->response->item($designCase, new DesignCaseTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {delete} /designCase/3 根据公司案例ID删除案例
     * @apiVersion 1.0.0
     * @apiName designCase delete
     * @apiGroup designCase
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
     *
     */
    public function destroy($id)
    {
        //检验是否存在该案例
        $case = DesignCaseModel::find($id);
        if (!$case) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        //检验是否是当前用户创建的案例
        if ($case->user_id != $this->auth_user_id) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        $designCase = $case->delete();
        if (!$designCase) {
            return $this->response->array($this->apiError());
        }
        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {get} /designCase/designCompany/{design_company_id}  根据设计公司id查看案例
     * @apiVersion 1.0.0
     * @apiName designCase lists
     * @apiGroup designCase
     *
     */
    public function lists($design_company_id)
    {
        $design = DesignCompanyModel::find($design_company_id);
//        if (!$design->isRead($this->auth_user_id, $design_company_id)) {
//            return $this->response->array($this->apiSuccess('没有权限访问', 403));
//
//        }
        if (!$design) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        $designCase = $design->designCase->where('status', 1);
        return $this->response->collection($designCase, new DesignCaseListsTransformer)->setMeta($this->apiMeta());

    }

    /**
     * @api {get} /designCase/openLists  设计案例推荐列表
     * @apiVersion 1.0.0
     * @apiName designCase openLists
     * @apiGroup designCase
     *
     * @apiParam {integer} page 页码
     * @apiParam {integer} per_page  页面数量
     * @apiParam {integer} sort 创建时间排序 0.创建时间倒序；1.创建时间正序；2.推荐倒序；5.随机数正序；
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     *   {
     *    "data": [
     *  {
     *      "id": 23,
     *      "prize": 1,
     *      "prize_val": "德国红点设计奖",
     *      "title": "1",
     *      "prize_time": "1991-01-20",
     *      "sales_volume": 1,
     *      "sales_volume_val": "100-500w",
     *      "customer": "1",
     *      "field": 2,
     *      "field_val": "消费电子",
     *      "profile": "1",
     *      "status": 0,
     *      "case_image": [],
     *      "industry": 2,
     *      "industry_val": "消费零售",
     *      "type": 1,
     *      "type_val": "产品设计",
     *      "design_type": 1,
     *      "design_type_val": "产品策略",
     *      "other_prize": "",
     *      "mass_production": 1,
     *      "design_company":{},
     *      "cover": "",
     *      "cover_id": 2,
     *      "patent": [{'time':2018-1-1,'type': 1}], // 获得专利 1.发明专利；2.实用新型专利；3.外观设计专利
     *      "prizes": [{'time':2018-1-1,'type': 1}], // 奖项:1.德国红点设计奖;2.德国IF设计奖;3.IDEA工业设计奖;4.中国红星奖;5.中国红棉奖;6.台湾金点奖;7.香港DFA设计奖 ;8.日本G-Mark设计奖;9.韩国好设计奖;10.新加坡设计奖;11.意大利—Compasso dOro设计奖;12.英国设计奖;13.中国优秀工业设计奖；14.DIA中国设计智造大奖；15.中国好设计奖；16.澳大利亚国际设计奖;20:其他
     *      "design_company": {
     *          "id":1,
     *          "company_name": "公司名称",
     *          "company_abbreviation": "公司简称",
     *          "logo": 1,
     *          "logo_image": {},    //logo图片
     *      }
     *  }
     * ],
     *      "meta": {
     *      "message": "Success",
     *      "status_code": 200
     *      }
     *   }
     *
     */
    public function openLists(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        $sort = $request->input('sort');

        $query = DesignCaseModel::with('DesignCompany')
            ->where('status', 1)->where('open', 1);

        //排序
        switch ($sort) {
            case 0:
                $query->orderBy('id', 'desc');
                break;
            case 1:
                $query->orderBy('id', 'asc');
                break;
            case 2:
                $query->orderBy('open_time', 'desc');
                break;
            case 5:
                $query->orderBy('random', 'asc');
                break;
            case 6:
                $query->orderBy('recommended_on', 'desc');
                break;
            default:
                $query->orderBy('id', 'desc');
        }


        $lists = $query->paginate($per_page);

        return $this->response->paginator($lists, new DesignCaseListsTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {put} /designCase/imageSummary  设计案例图片添加描述
     * @apiVersion 1.0.0
     * @apiName designCase imageSummary
     * @apiGroup designCase
     *
     * @apiParam {integer} asset_id 图片ID
     * @apiParam {string} summary 描述
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     *   }
     */
    public function imageSummary(Request $request)
    {
        $this->validate($request, [
            'asset_id' => 'required|integer',
            'summary' => 'required|max:100',
        ]);

        $asset = AssetModel::find($request->input('asset_id'));
        if (!$asset) {
            return $this->response->array($this->apiError("not found asset", 404));
        }

        $asset->summary = $request->input("summary");
        if (!$asset->save()) {
            return $this->response->array($this->apiError());
        } else {
            return $this->response->array($this->apiSuccess());
        }

    }

}
