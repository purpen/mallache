<?php

namespace App\Http\Controllers\Api\V1;

use App\Helper\MassageException;
use App\Http\Transformer\DesignProjectTransformer;
use App\Models\DesignProject;
use App\Models\User;
use Illuminate\Http\Request;

class DesignProjectController extends BaseController
{
    public function create(Request $request)
    {
        try {
            $user_id = $this->auth_user_id;

            if (!$this->auth_user->isDesignAdmin()) {
                throw new MassageException('无权限', 403);
            }
            if (!$design_company_id = User::designCompanyId($user_id)) {
                throw new MassageException('无权限', 403);
            }

            $this->validate($request, [
                'name' => 'required|max:100',
                'description' => 'max:6500',
                'level' => 'required|integer|in:1,2,3',
            ]);

            $design_project = new DesignProject();
            $design_project->name = $request->input('name');
            $design_project->description = $request->input('description');
            $design_project->level = $request->input('level');
            $design_project->user_id = $user_id;
            $design_project->design_company_id = $design_company_id;
            $design_project->save();

        } catch (MassageException $e) {
            return $this->response->array($this->apiError($e->getMessage(), $e->getCode()));
        }


        return $this->response->item($design_project, new DesignProjectTransformer())->setMeta($this->apiMeta());
    }


    /**
     * id    int(10)    否
     * name    varchar(100)    否        项目名称
     * description    text    是    ‘’    项目描述
     * level    tinyint(4)        null    项目等级 1.普通 2.紧急 3.非常紧急
     * business_manager    int(10)        0    商务经理
     * leader    int(10)        0    项目负责人
     * cost    decimal(10,2)        null    项目费用
     * workplace    varcahr(50)        ''    工作地点
     * type_value    varchar(100)        ''    设计类别
     * field    tinyint(4)    是    null    产品所属领域
     * industry    tinyint(4)    是    null    产品所属行业
     * start_time    int(10)        null    项目开始时间
     * project_duration    int(10)        null    项目工期
     * company_name    varchar(100)        ''    客户名称
     * contact_name    varchar(50)    是    ‘’    联系人姓名
     * position    varchar(50)        ''    职位
     * phone    varcahr(20)        ''    手机
     * province    int(10)        null    省份
     * city    int(10)        null    城市
     * area    int(10)        null    地区
     * address    varchar(100)        'null    详细地址
     * type    tinyint(4)    否        1.线上项目生成 2.线下项目
     * user_id    int(10)    否        创建人
     */
    public function update(Request $request)
    {
        try {
            $id = $request->input('id');
            $user_id = $this->auth_user_id;
            $design_project = DesignProject::find($id);

            // 判断权限
            if (!$design_project->isPower($user_id)) {
                throw new MassageException('无权限', 403);
            }

            $this->validate($request, [
                'id' => 'required|integer',
                'name' => 'required|max:100',
                'description' => 'max:6500',
                'level' => 'required|integer|in:1,2,3',
                'business_manager' => 'integer',
                'leader' => 'integer',
                'cost' => 'numeric',
                'workplace' => 'string|max:50',
                'type_value' => 'string|max:100',
                'field' => 'integer',
                'industry' => 'integer',
                'start_time' => 'integer',
                'project_duration' => 'integer',
                'company_name' => 'max:100',
                'contact_name' => 'max:50',
                'position' => 'max:50',
                'phone' => 'max:20',
                'province' => 'integer',
                'city' => 'integer',
                'area' => 'integer',
                'address' => 'string|max:100'
            ]);


            $arr = $request->all();
            DesignProject::updated($arr);

        } catch (MassageException $e) {
            return $this->response->array($this->apiError($e->getMessage(), $e->getCode()));
        }

        return $this->response->item($design_project, new DesignProjectTransformer())->setMeta($this->apiMeta());
    }

    // 详情展示
    public function show(Request $request)
    {
        $id = $request->input('id');

    }

}