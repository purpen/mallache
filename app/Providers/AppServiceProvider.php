<?php

namespace App\Providers;

use App\Models\OperationLog;
use App\Observers\OperationLogObservers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // sql 记录
//        DB::listen(function ($query) {
//            Log::info($query->sql);
//            Log::info($query->bindings);
//            Log::info($query->time);
//
//        });

        Schema::defaultStringLength(191);

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
