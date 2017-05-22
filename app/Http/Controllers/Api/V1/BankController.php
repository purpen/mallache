<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\BankTransformer;
use App\Models\Bank;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BankController extends BaseController
{
    /**
     * @api {get} /bank 用户id查看银行卡列表
     * @apiVersion 1.0.0
     * @apiName bank index
     * @apiGroup bank
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
        {
            "data": {
                "id": 1,
                "user_id": 1,
                "account_name": "北京银行",
                "account_bank_id": 1,
                "branch_name": "酒仙桥支行",
                "account_number": "110",
                "province": 1,
                "city": 1,
                "status": 0,
                "summary": "",
            },
            "meta": {
                "message": "Success",
                "status_code": 200
            }
        }
     */
    public function index()
    {
        $user_id = intval($this->auth_user_id);
        $bank = Bank::where('user_id' , $user_id)->where('status' , 0)->get();
        if(!$bank){
            return $this->response->array($this->apiError('not found bank', 404));
        }
        return $this->response->collection($bank, new BankTransformer())->setMeta($this->apiMeta());

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
     * @api {post} /bank 保存银行卡信息
     * @apiVersion 1.0.0
     * @apiName bank store
     * @apiGroup bank
     *
     * @apiParam {string} account_name 开户名
     * @apiParam {integer} account_bank_id 开户银行
     * @apiParam {string} branch_name 支行名称
     * @apiParam {string} account_number 支行账号
     * @apiParam {integer} province 省份
     * @apiParam {integer} city 城市
     * @apiParam {string} summary 备注
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
        {
            "data": {
                "id": 1,
                "user_id": 1,
                "account_name": "北京银行",
                "account_bank_id": 1,
                "branch_name": "酒仙桥支行",
                "account_number": "110",
                "province": 1,
                "city": 1,
                "status": 0,
                "summary": "",
            },
            "meta": {
                "message": "Success",
                "status_code": 200
            }
        }
     */
    public function store(Request $request)
    {
        $rules = [
            'account_name' => 'required|max:30',
            'account_bank_id' => 'required',
            'branch_name' => 'required|max:30',
            'account_number' => 'required|max:50',
            'province' => 'required|integer',
            'city' => 'required|integer',
        ];
        $all = $request->all();
        $all['user_id'] = $this->auth_user_id;
        $all['status'] = 0;
        $all['summary'] = $request->input('summary') ?? '';
        $validator = Validator::make($all, $rules);
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        try{
            $bank = Bank::create($all);
        }
        catch (\Exception $e){
            return $this->response->array($this->apiError());
        }

        return $this->response->item($bank, new BankTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /bank/{band_id} 银行卡信息详情
     * @apiVersion 1.0.0
     * @apiName bank show
     * @apiGroup bank
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
        {
            "data": {
                "id": 1,
                "user_id": 1,
                "account_name": "北京银行",
                "account_bank_id": 1,
                "branch_name": "酒仙桥支行",
                "account_number": "110",
                "province": 1,
                "city": 1,
                "status": 0,
                "summary": "",
            },
            "meta": {
                "message": "Success",
                "status_code": 200
            }
        }
     */
    public function show($bank_id)
    {
        $bank = Bank::where('id' , $bank_id)->first();
        if(!$bank){
            return $this->response->array($this->apiError('not found bank', 404));
        }
        return $this->response->item($bank, new BankTransformer())->setMeta($this->apiMeta());

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function edit(Bank $bank)
    {
        //
    }

    /**
     * @api {put} /bank/{bank_id} 更改银行卡信息
     * @apiVersion 1.0.0
     * @apiName bank put
     * @apiGroup bank
     *
     * @apiParam {string} account_name 开户名
     * @apiParam {integer} account_bank_id 开户银行
     * @apiParam {string} branch_name 支行名称
     * @apiParam {string} account_number 支行账号
     * @apiParam {integer} province 省份
     * @apiParam {integer} city 城市
     * @apiParam {string} summary 备注
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
        {
            "data": {
                "id": 1,
                "user_id": 1,
                "account_name": "北京银行",
                "account_bank_id": 1,
                "branch_name": "酒仙桥支行",
                "account_number": "110",
                "province": 1,
                "city": 1,
                "status": 0,
                "summary": "",
            },
            "meta": {
                "message": "Success",
                "status_code": 200
            }
        }
     */
    public function update(Request $request, $bank_id)
    {
        $rules = [
            'account_name' => 'required|max:30',
            'account_bank_id' => 'required',
            'branch_name' => 'required|max:30',
            'account_number' => 'required|max:50',
            'province' => 'required|integer',
            'city' => 'required|integer',
        ];
        $all = $request->except(['token']);
        $all['summary'] = $request->input('summary') ?? '';
        $validator = Validator::make($all, $rules);
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }
        $bank = Bank::where('id', $bank_id)->first();
        if (!$bank) {
            return $this->response->array($this->apiError('not found bank', 404));
        }
        $bank->update($all);
        return $this->response->item($bank, new BankTransformer())->setMeta($this->apiMeta());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bank $bank)
    {
        //
    }


    /**
     * @api {put} /bank/un/status 银行卡关闭
     * @apiVersion 1.0.0
     * @apiName bank unStatus
     * @apiGroup bank
     *
     * @apiParam {integer} id
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *  "meta": {
     *    "code": 200,
     *    "message": "Success.",
     *  }
     * }
     */
    public function unStatus(Request $request)
    {
        $id = $request->input('id');
        $bank = Bank::where('id', $id)->first();
        if (!$bank) {
            return $this->response->array($this->apiError('not found bank', 404));
        }
        $status = Bank::status($id, -1);
        if (!$status) {
            return $this->response->array($this->apiError('修改失败', 500));
        }
        return $this->response->array($this->apiSuccess());
    }
}
