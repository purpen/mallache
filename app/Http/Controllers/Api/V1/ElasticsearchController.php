<?php

namespace App\Http\Controllers\Api\V1;

use DB;
use Illuminate\Http\Request;
use App\Service\Elasticsearch;
use Elasticsearch\ClientBuilder;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ElasticsearchController extends Controller
{
    public function __construct()
    {
        $this->client = ClientBuilder::create()->build();
    }

    public function get_ceshi(Request $request)
    {
        $table = 'article';
        $client = $this->client;
        //删除索引
        $es = new Elasticsearch;
        //return $es->deleteIndex($table);
        /*$params = [
            'index' => 'accounts',
            'body' => [
                'settings' => [
                    'number_of_shards' => 3,
                    'number_of_replicas' => 2
                ],
                "person"=> [
                    "properties"=> [
                        "user"=> [
                            "type"=> "text",
                            "analyzer"=> "ik_max_word",
                            "search_analyzer"=> "ik_max_word"
                        ],
                        "title"=> [
                            "type"=> "text",
                            "analyzer"=> "ik_max_word",
                            "search_analyzer"=> "ik_max_word"
                        ],
                        "desc"=> [
                            "type"=> "text",
                            "analyzer"=> "ik_max_word",
                            "search_analyzer"=> "ik_max_word"
                        ]
                    ]
                ]
            ]
        ];
        dump($params);
        //创建索引
        $index = $client->indices()->create($params);
        dd($index);*/

        //添加索引文档
        /*$params = [
            'index' => 'accounts',
            'type' => 'text',
            'id' => 2,
            'body' => [
                'user' => '今天我们是',
                'title' => '今天天气格外的冷',
                'desc' => '9月10号是个大晴天啊',
            ]
        ];

        $index = $client->index($params);

        return $index;*/

        // Set the index and type
        /*$params = [
            'index' => 'index',
            'type' => 'text',
            'body' => [
                "_all"=> [
                    "analyzer"=> "ik_max_word",
                    "search_analyzer"=> "ik_max_word",
                    "term_vector"=> "no",
                    "store"=> "false"
                ],
                "properties"=> [
                    "content"=>[
                        "type"=> "string", //字段的类型为string，只有string类型才涉及到分词
                        "store"=> "no", //定义字段的存储方式，no代表不单独存储，查询的时候会从_source中解析。当你频繁的针对某个字段查询时，可以考虑设置成true
                        "term_vector"=> "with_positions_offsets", //定义了词的存储方式，with_position_offsets，意思是存储词语的偏移位置，在结果高亮的时候有用
                        "analyzer"=> "ik_max_word", //定义了索引时的分词方法
                        "search_analyzer"=> "ik_max_word", //定义了搜索时的分词方法
                        "include_in_all"=> "true", //定义了是否包含在_all字段中
                        "boost"=> 8 //是跟计算分值相关的
                    ]
                ]
            ]
        ];

        return $client->indices()->putSettings($params);*/

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
                    //添加索引文档
                    $index = $client->index($params);
                }
            }
        });*/

        $params = [
            'index' => $table,
            'type' => $table,
            'id' => 1, //索引id
            'client'=> ['ignore'=> [400,404]] //异常报错
        ];

        //索引文档内容
        return $client->get($params);

        //return $client->indices()->getSettings(['index'=>'article']);
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
        //return $params;
        //搜索
        $res = $es->searchIndex('今天不');
        return $res;
        $index['index'] = 'yuluo';
        $index['type'] = 'yuluo';
        //索引id
        $index['id'] = 'IhY1emUBaFK-zgeGAUDI';
        //删除单个索引文档
        return $this->deleteIndex($index);


    }


    public function search(Request $request)
    {
        $request = $request->all();

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
            $es = new Elasticsearch;
            $data = $this->client->indices()->create($params);
            if(isset($data['error']) || isset($data['status'])){
                return 0; //创建索引失败
            }
            return 1;//创建索引成功
        }
    }

    /**
     * 全文搜索
     * $content 搜索内容 string
     * $limit   条数 int
     * $page    页数 int
     */
    public function searchIndex(Request $request)
    {
        $request = $request->all();
        $params = [
            'content' => 'required'
        ];
        $page = $request['page'] ?? 1;
        $limit = $request['limit'] ?? 10;
        $validator = Validator::make($request, $params);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }
        $es = new Elasticsearch;
        $list = $es->searchIndex($request['content'],$page,$limit);
        if(!empty($list)){
            return $list;
        }else{
            return [];
        }
    }

    public function addArticleIndex()
    {
        $es = new Elasticsearch;
        //return $es->deleteIndex('article');
        return $es->addArticleIndex();
    }

}
