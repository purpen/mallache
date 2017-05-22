<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\BankTransformer;
use App\Models\Bank;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BankController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @api {post} /bank 保存银行
     * @apiVersion 1.0.0
     * @apiName bank store
     * @apiGroup bank
     *
     * @apiParam {string} account_name 开户名
     * @apiParam {integer} bank_id 开户银行
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
                "bank_id": 1,
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
            'bank_id' => 'required',
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
     * Display the specified resource.
     *
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function show(Bank $bank)
    {
        //
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bank $bank)
    {
        //
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
}
