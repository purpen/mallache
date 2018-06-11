<?php

namespace App\Console\Commands;

use App\Models\ProductDesign;
use App\Models\UDesign;
use Illuminate\Console\Command;

class UpdateItemName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ItemName:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步需求项目名称到item表';

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
        // 工业设计表
        ProductDesign::query()->with('item')->chunk(1000, function ($designs) {
            foreach ($designs as $v) {
                $item = $v->item;
                if ($item) {
                    if (!empty($v->name)) {
                        $item->name = $v->name;
                        $item->save();
                    }
                }

            }
        });

        // UI设计
        UDesign::query()->with('item')->chunk(1000, function ($designs) {
            foreach ($designs as $v) {
                $item = $v->item;
                if ($item) {
                    if (!empty($v->name)) {
                        $item->name = $v->name;
                        $item->save();
                    }
                }

            }
        });

        $this->info("各设计类型中的name字段同步至item完成");
    }
}
