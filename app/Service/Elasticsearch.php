<?php

namespace App\Service;

use DB;
use App\Models\Article;
use App\Models\AwardCase;
use App\Models\AssetModel;
use App\Models\TrendReports;
use App\Models\DesignCaseModel;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Log;

class Elasticsearch
{
    public function __construct()
    {
        $this->client = ClientBuilder::create()->build();
    }

    public function get_ceshi()
    {
        $table = 'article';
        $client = $this->client;
        //删除索引
        return $this->deleteIndex($table);
        /*$params = [
            'index'=> $table,
            'body' => [
                'settings' => [
                    "analysis"=>[
                        "analyzer"=>[
                            "ik"=>[
                                "tokenizer"=>"ik_max_word" //创建索引时使用中文分词
                            ]
                        ]
                    ]
                ],
                "type"=>"text",
                "analyzer"=>"ik_max_word",
            ],
            'client'=> ['ignore'=> [400,404]] //异常报错
        ];
        dump($params);
        //创建索引
        $index = $client->indices()->create($params);
        dd($index);*/
        //添加索引文档
        /*$params = [
            'index' => $table,
            'type' => 'text',
            'id' => 7,
            'body' => [
                'content' => '我们才是真正的部落',
                //'title' => '荣耀'
            ]
        ];
        $index = $client->index($params);
        dd($index);*/

        //获取一个索引的配置信息
        return $client->indices()->getSettings(['index'=>'index']);

        //分块创建索引文档
        /*DB::table($table)->orderBy('id')->select('id','title','content')->chunk(100, function ($res) {
            if(!empty($res)){
                $client = ClientBuilder::create()->build();
                foreach ($res as $val){
                    $params = [
                        'index' => 'article',
                        'type' => 'text',
                        'id' => $val->id,
                    ];
                    $params['body'] = [
                        'content'=>$val->content,
                        'title'=>$val->title
                    ];

                    dump($params);

                    //添加索引文档
                    $index = $client->index($params);
                    dump($index);
                }
            }
        });*/
        /*$params = [
            'index' => 'index',
            'type' => 'text',
            'id' => 2, //索引id
            'client'=> ['ignore'=> [400,404]] //异常报错
        ];

        //索引文档内容
        return $client->get($params);*/

        /*unset($params['body']);
        //查询索引
        $res = $client->get($params);
        return $res;
        //获取'my_index'中所有类型的映射
        /*$params = [
            'index' => 'article',
            'client'=> ['ignore'=> [400,404]] //抛出异常
        ];
        $response = $client->indices()->getMapping($params);
        return $response;*/
        $params = [
            //'index' => 'ws', //索引名称
            //'type' => 'text', //索引类型
            'size' => 50, //条数
            //'scroll' => '5s',
            'from' => 1, //页数
            /*'sort' => [  // 排序
                'age' => 'desc'   //对age字段进行降序排序
            ],*/
            'body' => [
                'query' => [
                    /*'constant_score'=>[
                        'filter' => [ //过滤器，不会计算相关度，速度快
                            'term' => [ //精确查找，不支持多个条件
                                'content' => '列表内容'
                            ]
                        ]
                    ]*/
                    /*'match' => [
                        'content' => '糯言' //指定content字段搜索
                    ]*/
                    'query_string' => [
                        'query' => '的', //全局搜索(搜索所有字段)
                    ]
                ],
//                "highlight"=>[
//                    "pre_tags"=>"<tag1>", "<tag2>",
//                    "post_tags"=>"</tag1>", "</tag2>",
//                    "fields"=>[
//                        "content"=>[]
//                    ]
//                ]
            ]
        ];
        //return $params;
        //搜索
        $res = $client->search($params);
        return $res;
        $index['index'] = 'yuluo';
        $index['type'] = 'yuluo';
        //索引id
        $index['id'] = 'IhY1emUBaFK-zgeGAUDI';
        //删除单个索引文档
        return $this->deleteIndicesIndex($index);


    }

    /**
     * 删除整个索引
     * $index 索引名称 string
     */
    public function deleteIndex($index = '')
    {
        $data = $this->client->indices()->delete(['index'=>$index,'client'=> ['ignore'=> [400,404]]]);
        if(isset($data['error']) || isset($data['status'])){
            return 0; //删除失败
        }
        return 1;//删除成功
    }

    //删除单个索引文档
    public function deleteIndicesIndex($index = [])
    {
        if(empty($index)){
            return $this->client->delete($index);
        }
        return false;
    }

    /**
     * 添加中文索引
     * $index 索引名称 string
     * $type  索引类型 string
     * $ppl   分词 1:中文分词,2:英文 int
     */
    public function addMiddleIndex($index='',$type='',$ppl=1)
    {
        if(!empty($index)) {
            $params = [
                'index' => $index, //索引名称
            ];
            if($ppl == 1){
                //创建索引时使用中文分词
                $params['body'] = [
                    'type' => 'text',
                    'analyzer' => 'ik_max_word',//细分
                    'search_analyzer'=> 'ik_smart',
                    'settings' => [
                        'analysis' => [
                            'analyzer' => [
                                'ik' => [
                                    'tokenizer' => 'ik_max_word'
                                ]
                            ]
                        ]
                    ]
                ];
                if(!empty($type)){
                    $params['body']['type'] = $type; //索引类型
                }
            }
            $params['client'] = [
                'ignore'=> [400,404],
                'timeout'=>10 //设置超时
            ]; //异常报错
            //创建索引
            return $this->client->indices()->create($params);
        }
    }

    /**
     * 全文搜索
     * $content 搜索内容 string
     * $limit   条数 int
     * $page    页数 int
     */
    public function searchIndex($content,$page=1,$limit=10)
    {
        if(empty($content)){
            return [];
        }
        $params = [
            'size' => (int)$limit, //条数
            'from' => (int)$page, //页数
            'body' => [
                'query' => [
                    'query_string' => [
                        'query' => $content, //全局搜索(搜索所有字段)
                    ]
                ],
//                "highlight"=>[
//                    "pre_tags"=>"<tag1>", "<tag2>",
//                    "post_tags"=>"</tag1>", "</tag2>",
//                    "fields"=>[
//                        "content"=>[]
//                    ]
//                ]
            ]
        ];
        //搜索
        $data = $this->client->search($params);
        if(!empty($data) && isset($data['hits']['hits']) && !empty($data['hits']['hits'])){
            return $data['hits']['hits'];
        }
        return [];
    }

    /**
     * 添加文章索引
     */
    public function addArticleIndex()
    {
        $params = [
            'index' => 'article',
            'client'=> ['ignore'=> [400,404]] //抛出异常
        ];
        //查询索引信息
        $article = $this->client->indices()->getMapping($params);
        if(isset($article['article'])){
            $this->deleteIndex('article');
        }
        $params = [
            'index' => 'article',
            'body' => [
                'settings' => [
                    'number_of_shards' => 3, //分片
                    'number_of_replicas' => 2 //副本
                ],
                "mappings"=> [
                    'article'=>[ //索引类型
                        "properties"=> [
                            "content"=> [
                                "type"=> "text",
                                "analyzer"=> "ik_max_word",
                                "search_analyzer"=> "ik_smart"
                            ],
                            "title"=> [
                                "type"=> "text",
                                "analyzer"=> "ik_max_word",
                                "search_analyzer"=> "ik_smart"
                            ],
                            "label"=> [
                                "type"=> "text",
                                "analyzer"=> "ik_max_word",
                                "search_analyzer"=> "ik_smart"
                            ]
                        ]
                    ]
                ]
            ]
        ];
        //创建索引
        $index = $this->client->indices()->create($params);
        //添加索引文档
        if(isset($index['acknowledged']) && $index['acknowledged'] == 'true'){
            DB::table('article')
                ->orderBy('id')
                ->select('id','title','content','updated_at','label','cover_id')
                ->where(['type'=>1,'status'=>1])
                ->chunk(500, function ($res) {
                if(!empty($res)){
                    $index = [];
                    $asset = new AssetModel;
                    foreach ($res as $val){
                        $params = [
                            'index' => 'article',
                            'type' => 'article',
                            'id' => $val->id,
                            'body' =>[
                                'content'=>$val->content, //内容
                                'title'=>$val->title, //标题
                                'updated_at' => $val->updated_at ?? '', //更新时间
                                'label' => $val->label, //标签
                                'cover' =>'' //封面图
                            ]
                        ];
                        $img = $asset->getOneImage((int)$val->cover_id);
                        if(!empty($img)){
                            $params['body']['cover'] = $img['small'] ?? '';
                        }
                        //添加索引文档
                        $index[] = $this->client->index($params);
                    }
                }
            });
            Log::info('已生成文章索引');
            return 1;
        }
        Log::info('生成文章索引失败');
        return 0;
    }

    /**
     * 添加案例索引
     */
    public function addCaseIndex()
    {
        $params = [
            'index' => 'case',
            'client'=> ['ignore'=> [400,404]] //抛出异常
        ];
        //查询索引信息
        $article = $this->client->indices()->getMapping($params);
        if(isset($article['case'])){
            $this->deleteIndex('case');
        }
        $params = [
            'index' => 'case',
            'body' => [
                'settings' => [
                    'number_of_shards' => 3, //分片
                    'number_of_replicas' => 2 //副本
                ],
                "mappings"=> [
                    'case'=>[ //索引类型
                        "properties"=> [
                            "content"=> [
                                "type"=> "text",
                                "analyzer"=> "ik_max_word",
                                "search_analyzer"=> "ik_smart"
                            ],
                            "title"=> [
                                "type"=> "text",
                                "analyzer"=> "ik_max_word",
                                "search_analyzer"=> "ik_smart"
                            ],
                            "label"=> [
                                "type"=> "text",
                                "analyzer"=> "ik_max_word",
                                "search_analyzer"=> "ik_smart"
                            ],
                            "company_name"=> [
                                "type"=> "text",
                                "analyzer"=> "ik_max_word",
                                "search_analyzer"=> "ik_smart"
                            ]
                        ]
                    ]
                ]
            ]
        ];
        //创建索引
        $index = $this->client->indices()->create($params);
        //添加索引文档
        if(isset($index['acknowledged']) && $index['acknowledged'] == 'true'){
            DB::table('design_case')
                ->orderBy('id')
                ->select('id','title','profile','updated_at','label','cover_id','prizes','design_company_id')
                ->where(['open'=>1,'status'=>1])
                ->chunk(500, function ($res) {
                    if(!empty($res)){
                        $index = [];
                        $prize = config('constant.prize');
                        $asset = new AssetModel;
                        foreach ($res as $val){
                            $params = [
                                'index' => 'case',
                                'type' => 'case',
                                'id' => $val->id,
                                'body' =>[
                                    'content'=>$val->profile, //描述
                                    'title'=>$val->title, //标题
                                    'updated_at' => $val->updated_at ?? '', //更新时间
                                    'label' => $val->label, //标签
                                    'cover' =>'', //封面图
                                    'prizes' =>'' //奖项
                                ]
                            ];
                            $img = $asset->getOneImage((int)$val->cover_id);
                            if(!empty($img)){
                                //封面图
                                $params['body']['cover'] = $img['small'] ?? '';
                            }
                            $company = DB::table('design_company')->where(['id'=>(int)$val->design_company_id])->select('company_name')->first();
                            //公司名称
                            $params['body']['company_name'] = $company->company_name ?? '';
                            //奖项
                            if(!empty($val->prizes)){
                                $prizes = json_decode($val->prizes,1);
                                foreach ($prizes as $v){
                                    $type = $v['type'];
                                    if (array_key_exists($type, $prize)) {
                                        $params['body']['prizes'] = $prize[$type];
                                    }else{
                                        $params['body']['prizes'] = '';
                                    }
                                }
                            }
                            //保存索引文档
                            $index[] = $this->client->index($params);
                        }
                    }
                });
            Log::info('已生成案例索引');
            return 1;
        }
        Log::info('生成案例索引失败');
        return 0;
    }

    /**
     * 添加设计奖项索引
     */
    public function addAwardCaseIndex()
    {
        $params = [
            'index' => 'award_case',
            'client'=> ['ignore'=> [400,404]] //抛出异常
        ];
        //查询索引信息
        $article = $this->client->indices()->getMapping($params);
        if(isset($article['award_case'])){
            $this->deleteIndex('award_case');
        }
        $params = [
            'index' => 'award_case',
            'body' => [
                'settings' => [
                    'number_of_shards' => 3, //分片
                    'number_of_replicas' => 2 //副本
                ],
                "mappings"=> [
                    'award_case'=>[ //索引类型
                        "properties"=> [
                            "content"=> [
                                "type"=> "text",
                                "analyzer"=> "ik_max_word",
                                "search_analyzer"=> "ik_smart"
                            ],
                            "title"=> [
                                "type"=> "text",
                                "analyzer"=> "ik_max_word",
                                "search_analyzer"=> "ik_smart"
                            ],
                            "label"=> [
                                "type"=> "text",
                                "analyzer"=> "ik_max_word",
                                "search_analyzer"=> "ik_smart"
                            ],
                            "prize"=> [
                                "type"=> "text",
                                "analyzer"=> "ik_max_word",
                                "search_analyzer"=> "ik_smart"
                            ]
                        ]
                    ]
                ]
            ]
        ];
        //创建索引
        $index = $this->client->indices()->create($params);
        //添加索引文档
        if(isset($index['acknowledged']) && $index['acknowledged'] == 'true'){
            DB::table('award_case')
                ->orderBy('id')
                ->select('id','title','content','updated_at','tags','cover_id','category_id')
                ->where(['status'=>1])
                ->chunk(500, function ($res) {
                    if(!empty($res)){
                        $index = [];
                        $asset = new AssetModel;
                        $awardCase = config('constant.awardCase_category');
                        foreach ($res as $val){
                            $category_id = $val->category_id;
                            if (array_key_exists($category_id, $awardCase)) {
                                $prize = $awardCase[$category_id];
                            }else{
                                $prize = '';
                            }
                            $params = [
                                'index' => 'award_case',
                                'type' => 'award_case',
                                'id' => $val->id,
                                'body' =>[
                                    'content'=>$val->content, //内容
                                    'title'=>$val->title, //标题
                                    'updated_at' => $val->updated_at ?? '', //更新时间
                                    'label' => $val->tags, //标签
                                    'cover' =>'', //封面图
                                    'prize' =>$prize //奖项
                                ]
                            ];
                            $img = $asset->getOneImage((int)$val->cover_id);
                            if(!empty($img)){
                                $params['body']['cover'] = $img['small'] ?? '';
                            }
                            //保存索引文档
                            $index[] = $this->client->index($params);
                        }
                    }
                });
            Log::info('已生成案例索引');
            return 1;
        }
        Log::info('生成案例索引失败');
        return 0;
    }

    /**
     * 添加报告索引
     */
    public function addTrendReportsIndex()
    {
        $params = [
            'index' => 'trend_reports',
            'client'=> ['ignore'=> [400,404]] //抛出异常
        ];
        //查询索引信息
        $article = $this->client->indices()->getMapping($params);
        if(isset($article['trend_reports'])){
            $this->deleteIndex('trend_reports');
        }
        $params = [
            'index' => 'trend_reports',
            'body' => [
                'settings' => [
                    'number_of_shards' => 3, //分片
                    'number_of_replicas' => 2 //副本
                ],
                "mappings"=> [
                    'trend_reports'=>[ //索引类型
                        "properties"=> [
                            "title"=> [
                                "type"=> "text",
                                "analyzer"=> "ik_max_word",
                                "search_analyzer"=> "ik_smart"
                            ],
                            "label"=> [
                                "type"=> "text",
                                "analyzer"=> "ik_max_word",
                                "search_analyzer"=> "ik_smart"
                            ]
                        ]
                    ]
                ]
            ]
        ];
        //创建索引
        $index = $this->client->indices()->create($params);
        //添加索引文档
        if(isset($index['acknowledged']) && $index['acknowledged'] == 'true'){
            DB::table('award_case')
                ->orderBy('id')
                ->select('id','title','updated_at','tags','cover_id')
                ->where(['status'=>1])
                ->chunk(500, function ($res) {
                    if(!empty($res)){
                        $index = [];
                        $asset = new AssetModel;
                        foreach ($res as $val){
                            $params = [
                                'index' => 'trend_reports',
                                'type' => 'trend_reports',
                                'id' => $val->id,
                                'body' =>[
                                    'title'=>$val->title, //标题
                                    'updated_at' => $val->updated_at ?? '', //更新时间
                                    'label' => $val->tags, //标签
                                    'cover' =>'', //封面图
                                ]
                            ];
                            $img = $asset->getOneImage((int)$val->cover_id);
                            if(!empty($img)){
                                $params['body']['cover'] = $img['small'] ?? '';
                            }
                            //保存索引文档
                            $index[] = $this->client->index($params);
                        }
                    }
                });
            Log::info('已生成案例索引');
            return 1;
        }
        Log::info('生成案例索引失败');
        return 0;
    }

    /**
     * 保存报告
     * $id 报告id
     */
    public function saveTrendReportsIndex($id)
    {
        $trend = TrendReports::where(['id'=>$id,'status'=>1])->first();
        if(!empty($trend)){
            $asset = new AssetModel;
            $params = [
                'index' => 'trend_reports',
                'type' => 'trend_reports',
                'id' => $trend->id,
                'client'=> ['ignore'=> [400,404,500]], //异常报错
                'body' =>[
                    'title'=>$trend->title, //标题
                    'updated_at' => $trend->updated_at ?? '', //更新时间
                    'label' => $trend->tags, //标签
                    'cover' =>'', //封面图
                ]
            ];
            $img = $asset->getOneImage((int)$trend->cover_id);
            if(!empty($img)){
                $params['body']['cover'] = $img['small'] ?? '';
            }
            //保存索引文档
            $index = $this->client->index($params);
            if(isset($index['error']) || isset($index['status'])){
                return 0;
            }
            return 1;
        }
        return 0;
    }

    /**
     * 保存设计奖项
     * $id 奖项id
     */
    public function saveAwardCaseIndex($id)
    {
        $award_case = AwardCase::where(['id'=>$id,'status'=>1])->first();
        if(!empty($award_case)){
            $asset = new AssetModel;
            $awardCase = config('constant.awardCase_category');
            $category_id = $award_case->category_id;
            if (array_key_exists($category_id, $awardCase)) {
                $prize = $awardCase[$category_id];
            }else{
                $prize = '';
            }
            $params = [
                'index' => 'award_case',
                'type' => 'award_case',
                'id' => $award_case->id,
                'client'=> ['ignore'=> [400,404,500]], //异常报错
                'body' =>[
                    'content'=>$award_case->content, //内容
                    'title'=>$award_case->title, //标题
                    'updated_at' => $award_case->updated_at ?? '', //更新时间
                    'label' => $award_case->tags, //标签
                    'cover' =>'', //封面图
                    'prize' =>$prize //奖项
                ]
            ];
            $img = $asset->getOneImage((int)$award_case->cover_id);
            if(!empty($img)){
                $params['body']['cover'] = $img['small'] ?? '';
            }
            //保存索引文档
            $index = $this->client->index($params);
            if(isset($index['error']) || isset($index['status'])){
                return 0;
            }
            return 1;
        }
        return 0;
    }

    /**
     * 保存案例
     * $id 案例id
     */
    public function saveCaseIndex($id)
    {
        $case = DesignCaseModel::where(['id'=>$id,'status'=>1])->first();
        if(!empty($case)){
            $prize = config('constant.prize');
            $asset = new AssetModel;
            $params = [
                'index' => 'case',
                'type' => 'text',
                'id' => $case->id,
                'client'=> ['ignore'=> [400,404,500]], //异常报错
                'body' =>[
                    'content'=>$case->profile, //描述
                    'title'=>$case->title, //标题
                    'updated_at' => $case->updated_at ?? '', //更新时间
                    'label' => $case->label, //标签
                    'cover' =>'', //封面图
                    'prizes' =>'' //奖项
                ]
            ];
            $img = $asset->getOneImage((int)$case->cover_id);
            if(!empty($img)){
                //封面图
                $params['body']['cover'] = $img['small'] ?? '';
            }
            $company = DB::table('design_company')->where(['id'=>(int)$case->design_company_id])->select('company_name')->first();
            //公司名称
            $params['body']['company_name'] = $company->company_name ?? '';
            //奖项
            if(!empty($case->prizes)){
                $prizes = json_decode($case->prizes,1);
                foreach ($prizes as $v){
                    $type = $v['type'];
                    if (array_key_exists($type, $prize)) {
                        $params['body']['prizes'] = $prize[$type];
                    }else{
                        $params['body']['prizes'] = '';
                    }
                }
            }
            //保存索引文档
            $index = $this->client->index($params);
            if(isset($index['error']) || isset($index['status'])){
                return 0;
            }
            return 1;
        }
        return 0;
    }

    /**
     * 保存文章
     * $id 文章id
     */
    public function saveArticleIndex($id)
    {
        $article = Article::where(['id'=>$id,'status'=>1])->first();
        if(!empty($article)){
            $asset = new AssetModel;
            $params = [
                'index' => 'article',
                'type' => 'article',
                'id' => $article->id,
                'body' =>[
                    'content'=>$article->content, //内容
                    'title'=>$article->title, //标题
                    'updated_at' => $article->updated_at ?? '', //更新时间
                    'label' => $article->label, //标签
                    'cover' =>'' //封面图
                ]
            ];
            $img = $asset->getOneImage((int)$article->cover_id);
            if(!empty($img)){
                $params['body']['cover'] = $img['small'] ?? '';
            }
            //保存索引文档
            $index = $this->client->index($params);
            if(isset($index['error']) || isset($index['status'])){
                return 0;
            }
            return 1;
        }
        return 0;
    }

}