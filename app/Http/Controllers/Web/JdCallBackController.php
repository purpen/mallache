<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Lib\JdPay\JdPay;

class JdCallBackController extends Controller
{
    public function callUrl(Request $request)
    {
        $tradeNum = $request->input('tradeNum');

        $tradeNum = JdPay::deStr($tradeNum);

        $url = config('jdpay.z_callbackUrl');
        header("Location: $url?out_trade_no=$tradeNum");
    }
}