<?php

namespace App\Http\Transformer;

use App\Models\Notice;
use League\Fractal\TransformerAbstract;

class NoticeTransformer extends TransformerAbstract
{
    /*
        id	            int(10)	        否		ID
        user_id	        int(10)	        否		用户ID
        title	        varchar(50)	    否		标题
        content	        text(4)	    是		内容
        summary	        varchar	    是		备注
        url	            varchar	    否		链接
        cover_id        int(11)     否    封面ID
        cover           array       否    图片对象
        evt	          tinyint(4)	  否		类型：0.全部；1.需求方；2.设计公司；
        type	          tinyint(4)	    否		类型：1.默认；2.--；
        status	    tinyint(4)	    是		状态：0.禁用；1.启用；
    */

    // 系统通知列表
    public function transform(Notice $notice)
    {
        return [
            'id' => $notice->id,
            'type' => $notice->type,
            'evt' => $notice->evt,
            'evt' => $notice->evt,
            'url' => $notice->url,
            'title' => $notice->title,
            'user_id' => $notice->user_id,
            'cover_id' => $notice->cover_id,
            'cover' => $notice->cover,
            'summary' => $notice->summary,
            'content' => $notice->content,
            'status' => $notice->status,
            'created_at' => $notice->created_at,
        ];
    }

}
