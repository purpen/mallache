<?php

namespace App\Service;

use DB;
use Elasticsearch\ClientBuilder;

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
        //return $client->indices()->getSettings(['index'=>$table]);

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
        return $this->client->indices()->delete(['index'=>$index,'client'=> ['ignore'=> [400,404]]]);
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
     * 添加中分索引
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
            if(!empty($type)){
                $params['type'] = $type; //索引类型
            }
            if($ppl == 1){
                //创建索引时使用中文分词
                $params['body'] = [
                    'analyzer' => 'ik_max_word',
                    'settings' => [
                        "analysis" => [
                            'analyzer' => [
                                'ik' => [
                                    'tokenizer' => 'ik_max_word'
                                ]
                            ]
                        ]
                    ]
                ];
            }
            $params['client'] = ['ignore'=> [400,404]]; //异常报错
            //创建索引
            return $this->client->indices()->create($params);
        }
    }

    /**
     * 添加中分索引
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
            /*'sort' => [  // 排序
                'age' => 'desc'   //对age字段进行降序排序
            ],*/
            'body' => [
                'query' => [
                    /*'match' => [
                        'content' => '糯言' //指定content字段搜索
                    ]*/
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
     * $content 搜索内容 string
     */
    public function addArticleIndex()
    {
        //创建index索引
        $index = $this->addMiddleIndex($index='',$type='',$ppl=1);
        DB::table(article)->orderBy('id')->select('id','title','content')->chunk(1000, function ($res) {
            if(!empty($res)){
                $index = [];
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
                    //添加索引文档
                    $index[] = $this->client->index($params);
                }
                return $index;
            }
        });
    }
}