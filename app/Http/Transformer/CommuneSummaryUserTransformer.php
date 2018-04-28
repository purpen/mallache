<?php

namespace App\Http\Transformer;

use App\Models\DesignCompanyModel;
use League\Fractal\TransformerAbstract;
use App\Models\CommuneSummaryUser;

class CommuneSummaryUserTransformer extends TransformerAbstract
{
    /*
id	int(10)	否
commune_summary_id	int(11)	是		所属沟通纪要ID
user_id	int(11)	是		创建者用户ID
selected_user_id	int(11)	是		被选中的用户
type	tinyint(1)	是	1	类型;1.默认；
status	tinyint(1)	是	1	状态：0.禁用；1.启用；
    */

    public function transform(CommuneSummaryUser $communeSummaryUser)
    {
        return [
            'id' => intval($communeSummaryUser->id),
            'commune_summary_id' => intval($communeSummaryUser->commune_summary_id),
            'user_id' => intval($communeSummaryUser->user_id),
            'selected_user_id' => intval($communeSummaryUser->selected_user_id),
            'type' => intval($communeSummaryUser->type),
            'status' => intval($communeSummaryUser->status),
            'created_at' => $communeSummaryUser->created_at,
            'user' => $communeSummaryUser->user,
        ];
    }
}
