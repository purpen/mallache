<?php

namespace Lib\AiBaiDu;

require_once "AipNlp.php";

class Api
{
    private $APP_ID;
    private $API_KEY;
    private $SECRET_KEY;

    private $client = null;

    public function __construct()
    {
        $this->APP_ID = config("baiduapi.ai.app_id", 11326965);
        $this->API_KEY = config("baiduapi.ai.api_key", 'VdcCcMFLe43vMBsm9iwxOICF');
        $this->SECRET_KEY = config("baiduapi.ai.secret", 'GCe0DTeAGEgXivGeiGWQzTomQoGEGb8n');

        $this->client = new \AipNlp($this->APP_ID, $this->API_KEY, $this->SECRET_KEY);
    }

    //短文本相似度接口
    public function nlp($text1, $text2, $model = "BOW")
    {
        return $this->client->simnet($text1, $text2, ['model' => $model]);
    }
}