<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\FundLogTransformer;
use App\Models\FundLog;
use Illuminate\Http\Request;

class FundLogController extends BaseController
{
    /**
     * @api {get} /fundLogList  用户id查看资金流水
     * @apiVersion 1.0.0
     * @apiName FundLog index
     * @apiGroup FundLog
     *
     * @apiParam {string} token
     * @apiParam {integer} per_page 分页数量  默认15
     * @apiParam {integer} page 页码
     * @apiParam {integer} sort 0.升序；1.降序（默认）；
     * @apiParam {integer} type 交易类型 -1：出账；1.入账
     * @apiParam {integer} transaction_type 交易对象类型： 1.自平台；2.支付宝；3.微信；4：京东；5.银行转账
     */
    public function index(Request $request)
    {
        //type 交易类型 -1：出账；1.入账
        $type = in_array($request->input('type'), [-1, 1]) ? $request->input('type') : null;
        //交易对象类型 1.自平台；2.支付宝；3.微信；4：京东；5.银行转账
        $transaction_type = in_array($request->input('transaction_type'), [1, 2, 3, 4, 5]) ? $request->input('transaction_type') : null;
        $per_page = $request->input('per_page') ?? $this->per_page;

        if ($request->input('sort') == 0 && $request->input('sort') !== null) {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $fundLog = FundLog::where('user_id', $this->auth_user_id);

        if($type !== null){
            $fundLog->where('type' , $type);
        }
        if($transaction_type !== null){
            $fundLog->where('transaction_type' , $transaction_type);
        }

        $lists = $fundLog->orderBy('id', $sort)->paginate($per_page);

        return $this->response->paginator($lists , new FundLogTransformer())->setMeta($this->apiSuccess());

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
