<?php

namespace Lib\AiBaiDu;

require_once "AipNlp.php";

class Api
{
    private $APP_ID = '11318362';
    private $API_KEY = 'VBAC0sCwZhxnTQ5tp5USdiwh';
    private $SECRET_KEY = 't9oAFOdRk394QTTBVmlUE5XLbHGFyqGc';

    private $client = null;

    public function __construct()
    {
        $this->client = new \AipNlp($this->APP_ID, $this->API_KEY, $this->SECRET_KEY);
    }

    //短文本相似度接口
    public function nlp($text1, $text2, $model = "BOW")
    {

        return $this->client->simnet($text1, $text2, ['model' => $model]);
    }
}