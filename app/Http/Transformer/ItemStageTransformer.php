<?php

namespace App\Http\Transformer;

use App\Models\ItemStage;
use League\Fractal\TransformerAbstract;

class ItemStageTransformer extends TransformerAbstract
{
    /**
     * id                    int(10)            否
     * item_id                int(10)            否        项目ID
     * design_company_id    int(10)            否        设计公司ID
     * title                varchar(50)        否        阶段名称
     * content                varchar(500)    否        内容描述
     * summary                varcha(100)        是    ''    备注
     * status              tinyint(4)        否    0    是否发布：0.否；1.是;
     */
    public function transform(ItemStage $item_stage)
    {
        return $item_stage->info();
    }

}