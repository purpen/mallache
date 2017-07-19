<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Lib\YunPianSdk\Yunpian;

class SendOneSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $mobile = null;

    protected $text = null;

    /**
     * 任务最大尝试次数
     *
     * @var int
     */
    public $tries = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $mobile, string $text)
    {
        $this->mobile = $mobile;
        $this->text = $text;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $yun_pian = new Yunpian();
        $result = $yun_pian->sendOneSms($this->mobile, $this->text);

        if(intval($result->statusCode) !== 200){
            Log::error('短信发送：' . json_encode($result));
            throw new \Exception($result->error,$result->statusCode);
        }

        unset($yun_pian, $result);
    }

    public function failed(\Exception $exception)
    {
        //
    }
}
