<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JdCallBackController extends Controller
{
    public function callUrl(Request $request)
    {
        $tradeNum = $request->input('tradeNum');

        header("Location: http://mc.taihuoniao.com/jdpay/callback?out_trade_no=$tradeNum");
    }
}