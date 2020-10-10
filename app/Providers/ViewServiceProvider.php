<?php

namespace App\Providers;

// use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\View\ViewServiceProvider as BaseViewServiceProvider;

class ViewServiceProvider extends BaseViewServiceProvider
{

    public function register()
    {
        $this->app['config']['view.paths'] = array_merge(config('hpsynapse.view_path',[]),$this->app['config']['view.paths']);
        parent::register();
    }

}