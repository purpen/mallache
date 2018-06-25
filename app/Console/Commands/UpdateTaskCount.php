<?php

namespace App\Console\Commands;

use App\Models\DesignCompanyModel;
use App\Models\DesignProject;
use App\Models\Task;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateTaskCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'taskCount:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新项目下任务总数量，完成未完成数量的统计';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $design_projects = DesignProject::where('status' , 1)->get();
        foreach ($design_projects as $design_project){
            //总数
            $task_count = Task::where('item_id' , $design_project->id)->where('status' , 1)->count();
            //未完成
            $task_no_count = Task::where('item_id' , $design_project->id)->where('status' , 1)->where('stage' , 0)->count();
            //完成
            $task_ok_count = Task::where('item_id' , $design_project->id)->where('status' , 1)->where('stage' , 2)->count();

            $design_project->task_count = $task_count;
            $design_project->task_ok_count = $task_ok_count;
            $design_project->task_no_count = $task_no_count;
            $design_project->save();
        }
        $this->info("同步完项目下面任务总数，未完成/已完成的数量统计");
    }
}
