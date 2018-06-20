<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Models\ProductDesign;
use App\Models\UDesign;
use Illuminate\Console\Command;

class UpdateItemUProductDesign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:itemUProduct';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步产品UI老数据到项目表中';

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
                        $item->cycle = $v->cycle;
                        $item->design_cost = $v->design_cost;
                        $item->industry = $v->industry;
                        $item->item_province = $v->province;
                        $item->item_city = $v->city;
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
                        $item->cycle = $v->cycle;
                        $item->design_cost = $v->design_cost;
                        $item->industry = $v->industry;
                        $item->item_province = $v->province;
                        $item->item_city = $v->city;
                        $item->save();
                    }
                }

            }
        });

        $this->info("各设计类型中的字段同步至item完成");

    }
}
