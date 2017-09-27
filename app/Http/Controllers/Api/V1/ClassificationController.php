<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\AdminTransformer\ClassificationTransformer;
use App\Models\Classification;
use Illuminate\Http\Request;

class ClassificationController extends BaseController
{
    /**
     * @api {get} /classification/list 分类列表
     * @apiVersion 1.0.0
     * @apiName classification index
     * @apiGroup classification
     *
     * @apiParam {integer} type *栏目类型：1.文章；
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     * }
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'type' => 'required|integer'
        ]);
        $list = Classification::where('type', $request->input('type'))
            ->where('status', 1)
            ->get();

        return $this->response->collection($list, new ClassificationTransformer())->setMeta($this->apiMeta());
    }
}