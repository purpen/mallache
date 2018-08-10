<?php


namespace App\Http\Controllers\Api\Wx;

use App\Helper\Tools;
use App\Http\Transformer\UserTransformer;
use App\Models\DemandCompany;
use App\Models\User;
use Dingo\Api\Exception\StoreResourceFailedException;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\ItemQuotation;
use App\Http\AdminTransformer\DemandCompanyTransformer;
class QuotationController extends BaseController
{
    /**
     * @api {post} /wechat/quotationAdd  查看报价
     * @apiVersion 1.0.0
     * @apiName WxquotationAdd quotationAdd
     * @apiGroup Wx
     *
     * @apiParam {integer} user_id 用户ID
     * @apiParam {integer} type 设计类型：1.产品设计；2.UI UX 设计；3. 平面设计 4.H5 5.包装设计 6.插画设计
     * @apiParam {json} design_types 设计类别：产品设计（1.产品策略；2.产品设计；3.结构设计；）UXUI设计（1.app设计；2.网页设计；3.'界面设计', 4 . '服务设计', 5 . '用户体验咨询'）平面设计（1.'logo/VI设计', 2.'海报/宣传册', 3 .'画册/书装'）H5(1.H5) 包装设计（1.包装设计）插画（1. '商业插画', 2. '书籍插画', 3. '形象/IP插画'）。[1,2]
     * @apiParam {integer} level 级别：1. 一般 2. 较高 3.优质
     */

    public function quotationAdd(Request $request)
    {
        // 验证规则
        $rules = [
            'user_id' => 'required|integer',
            'type' => 'required|integer|regex:/[1-6]/',
            'design_types' => 'JSON',
            'level' => 'required|integer|regex:/[1-3]/',
        ];

        $payload = $request->only('user_id', 'type',  'design_types','level');
        $validator = app('validator')->make($payload, $rules);

        // 验证格式
        if ($validator->fails()) {
            throw new StoreResourceFailedException('请求参数格式不对！', $validator->errors());
        }

        //获取数据
        $req = $request->all();
        $baojia = new ItemQuotation();
        $baojia->user_id = $req['user_id'];
        $baojia->type = $req['type'];
        $baojia->design_types = $req['design_types'];
        $baojia->level = $req['level'];
        $res = $baojia->save();
        
        //生成报价
        $arr = [];
        if (!$res) {
            return $this->response->error('error', 500);
        } elseif ($req['level'] == 1) {
            $arr = ['min' => 1, 'max' => 5];
        } elseif ($req['level'] == 2) {
            $arr = ['min' => 6, 'max' => 8];
        } else {
            $arr = ['min' => 9, 'max' => 10];
        }
        return $this->response->array($this->apiSuccess('Success', 200, $arr));
    }
}