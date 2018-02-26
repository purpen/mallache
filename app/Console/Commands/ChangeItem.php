<?php

namespace App\Console\Commands;

use App\Models\Item;
use Illuminate\Console\Command;

class ChangeItem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'item:designTypesChange';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '项目需求设计类型数据结构修改';

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
        Item::chunk(100, function($items){
            foreach ($items as $item){
                if(0 != $item->design_type && null == $item->design_types){
                    $item->design_types = json_encode([$item->design_type]);
                    $item->save();
                }
            }
        });

        $this->info('结构数据调整完毕');
    }
}
