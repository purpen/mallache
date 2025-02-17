<?php

namespace App\Http\Transformer;

use App\Models\CommuneSummary;
use App\Models\CommuneSummaryUser;
use App\Models\Task;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class CommuneSummaryTransformer extends TransformerAbstract
{
    /*
id	int(10)	否
user_id	int(10)	否	0	创建者ID
item_id	int(10)	否	0	项目ID
status	tinyint(4)		0	状态
title	varchar(100)	否		标题
content	varchar(1000)	否		内容
location	varchar(100)	否		定位
expire_time	date	是		到期时间
    */

    public function transform(CommuneSummary $cummuneSummary)
    {
        $selected_user = '';
        $cummuneSummaryUsers = CommuneSummaryUser::where('commune_summary_id' , $cummuneSummary->id)->get();
        if(!empty($cummuneSummaryUsers)){
            $selected_user_id = [];
            foreach ($cummuneSummaryUsers as $cummuneSummaryUser){
                $selected_user_id[] = $cummuneSummaryUser->selected_user_id;
            }
            $selected_user = User::whereIn('id' , $selected_user_id)->get();
        }
        return [
            'id' => intval($cummuneSummary->id),
            'title' => strval($cummuneSummary->title),
            'content' => $cummuneSummary->content,
            'location' => $cummuneSummary->location,
            'user_id' => intval($cummuneSummary->user_id),
            'item_id' => intval($cummuneSummary->item_id),
            'type' => intval($cummuneSummary->type),
            'status' => intval($cummuneSummary->status),
            'expire_time' => $cummuneSummary->expire_time,
            'other_realname' => $cummuneSummary->other_realname,
            'commune_image' => $cummuneSummary->commune_image,
            'realname' => $cummuneSummary->user->getUserName(),
            'logo_image' => $cummuneSummary->user ? $cummuneSummary->user->logo_image : '',
            'selected_user' => $selected_user,
            'created_at' => $cummuneSummary->created_at,
        ];
    }
}
