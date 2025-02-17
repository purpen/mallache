<?php

namespace App\Jobs;

use App\Models\Notice;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class SendUserNotice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $evt = null;
    protected $id = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($evt , $id)
    {
        $this->evt = (int)$evt;
        $this->id = (int)$id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //全部
        if ($this->evt === 0) {
            $query['evt'] = 0;
            User::whereIn('type' , [1,2])->chunk(200, function ($users) {
                foreach ($users as $user){
                    $user->increment('notice_count');
                }
            });
        } else {
            //需求或者设计
            $query['evt'] = $this->evt;
            User::where('type' , $query['evt'])->chunk(200, function ($users) {
                foreach ($users as $user){
                    $user->increment('notice_count');
                }
            });
        }


        $notice = Notice::find($this->id);
        $notice->status = 1;
        $notice->save();
    }
}
