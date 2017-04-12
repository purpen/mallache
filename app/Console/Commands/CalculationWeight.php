<?php

namespace App\Console\Commands;

use App\Models\DesignCompanyModel;
use Illuminate\Console\Command;
use App\Jobs\CalculationWeight as calculation;

class CalculationWeight extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Weighted:calculation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '计算所有设计公司权重值';

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
        $user_id_arr = DesignCompanyModel::select('user_id')
            ->where('status', 1)
            ->get()
            ->toArray();

        $bar = $this->output->createProgressBar(count($user_id_arr));

        for ($i = 0;$i < count($user_id_arr);$i++){
            dispatch(new calculation($user_id_arr[$i]));

            $bar->advance();
        }

        $bar->finish();
    }
}
