<?php
/**
 * Created by PhpStorm.
 * User: cailiguang
 * Date: 2018/10/24
 * Time: 下午4:35
 */

namespace App\Http\Controllers\Api\Admin;

use App\Http\AdminTransformer\AdminSmallItemListsTransformer;
use App\Http\Controllers\Controller;
use App\Models\SmallItem;
use Illuminate\Http\Request;

class AdminSmallItemController extends Controller
{
    /**
     * @api {get} /admin/smallItem/lists 小程序项目列表
     * @apiVersion 1.0.0
     * @apiName AdminSmallItem lists
     * @apiGroup AdminSmallItem
     *
     * @apiParam {integer} per_page 分页数量  默认15
     * @apiParam {integer} page 页码
     * @apiParam {integer} sort 0.升序；1.降序（默认）;2.推荐降序；
     * @apiParam {integer} is_ok 0.未解决; 1.已解决；10.全部;默认0；
     * @apiParam {string} token
     */
    public function lists(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        $sort = $request->input('sort') ? $request->input('sort') : 0;
        $is_ok = $request->input('is_ok') ? $request->input('is_ok') : 0;
        $query = SmallItem::query();
        if ($is_ok != 10) {
            $query->where('is_ok', $is_ok);
        }
        if ($sort == 0) {
            $query->orderBy('id', 'desc');
        } else {
            $query->orderBy('id', 'asc');
        }

        $lists = $query->paginate($per_page);

        return $this->response->paginator($lists, new AdminSmallItemListsTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /admin/smallItem/show 小程序项目详情
     * @apiVersion 1.0.0
     * @apiName AdminSmallItem show
     * @apiGroup AdminSmallItem
     *
     * @apiParam {integer} id 小程序项目id
     * @apiParam {string} token
     */
    public function show(Request $request)
    {
        $id = $request->input('id');
        $smallItem = SmallItem::find($id);
        if (!$smallItem) {
            return $this->response->array($this->apiError('not found smallItem', 404));
        }
        return $this->response->item($smallItem, new AdminSmallItemListsTransformer())->setMeta($this->apiMeta());

    }

    /**
     * @api {put} /admin/smallItem/update 小程序项目编辑
     * @apiVersion 1.0.0
     * @apiName AdminSmallItem update
     * @apiGroup AdminSmallItem
     *
     * @apiParam {integer} id 小程序项目id
     * @apiParam {integer} is_ok 0.未解决; 1.已解决；
     * @apiParam {string} summary 备注
     * @apiParam {string} token
     */
    public function update(Request $request)
    {
        $id = $request->input('id');
        $all['is_ok'] = $request->input('is_ok');
        $all['summary'] = $request->input('summary');
        $smallItem = SmallItem::find($id);
        if (!$smallItem) {
            return $this->response->array($this->apiError('not found smallItem', 404));
        }
        if ($smallItem->update($all)) {
            return $this->response->array($this->apiSuccess());
        }
        return $this->response->array($this->apiError('更改失败', 412));

    }

    /**
     * @api {delete} /admin/smallItem/delete 小程序项目删除
     * @apiVersion 1.0.0
     * @apiName AdminSmallItem delete
     * @apiGroup AdminSmallItem
     *
     * @apiParam {array} id 小程序项目id
     * @apiParam {string} token
     */
    public function delete(Request $request)
    {
        $id = (array)$request->input('id');
        $n = SmallItem::query()->whereIn('id', $id)->delete();
        return $this->response->array($this->apiSuccess());
    }
}
