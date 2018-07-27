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
    public function __construct($mobile, $text)
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
        Log::info(3331);
        try{
            if (!empty($this->mobile) && !empty($this->text)) {
                Log::info(3333);
                $yun_pian = new Yunpian();
                $result = $yun_pian->sendOneSms($this->mobile, $this->text);

                Log::info(3332);
                if (intval($result->statusCode) !== 200) {
                    Log::error('短信发送：' . json_encode($result));
                }

                unset($yun_pian, $result);
            } else {
                Log::error('短信发送参数为空');
            }
        }catch (\Exception $e){
            Log::error($e);
        }



    }

    public function failed(\Exception $exception)
    {
        //
    }
}
