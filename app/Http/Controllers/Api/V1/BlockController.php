<?php

namespace App\Http\Controllers\Api\V1;



use App\Http\Transformer\BlockTransformer;
use App\Models\Block;
use Illuminate\Http\Request;

class BlockController extends BaseController
{

    /**
     * @api {get} /block 详情
     * @apiVersion 1.0.0
     * @apiName block index
     * @apiGroup Block
     *
     * @apiParam {string} mark
     *
     * @apiSuccessExample 成功响应:
            {
                "data": [
                    {
                        "id": 1,
                        "name": "test",
                        "type": 1,
                        "status": 0,
                        "user_id": 0,
                        "code": 0,
                        "content": 0,
                        "summary": 0,
                        "count": 0
                    }
                ],
                "meta": {
                    "message": "Success",
                    "status_code": 200,
                    "pagination": {
                        "total": 1,
                        "count": 1,
                        "per_page": 10,
                        "current_page": 1,
                        "total_pages": 1,
                        "links": []
                    }
                }
            }
     */
    public function show(Request $request)
    {
        $mark = $request->input('mark') ? (int)$request->input('mark') : '';

        $block = Block::where('mark' , $mark)->first();

        if($block){
            return $this->response->item($block, new BlockTransformer())->setMeta($this->apiMeta());
        }else{
            return $this->response->array($this->apiError('not found', 404));
        }

    }


}
