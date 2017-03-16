<?php

/**
 * api 基类
 */
namespace App\Http\Controllers\Api\V1;

use App\Http\ApiHelper;
use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;

class BaseController extends Controller
{
    use Helpers;
    use ApiHelper;
}