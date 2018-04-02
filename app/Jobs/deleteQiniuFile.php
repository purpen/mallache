<?php

namespace App\Jobs;

use App\Helper\QiniuApi;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class deleteQiniuFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($str)
    {
        $this->path_str = $str;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        QiniuApi::yunPanDelete($this->path_str);
    }
}
