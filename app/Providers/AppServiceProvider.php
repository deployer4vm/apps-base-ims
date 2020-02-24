<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        
        if(config('AppConfig.system.public_path')){
            $this->app->bind('path.public', function() {
                return realpath(__DIR__.'/../..'.config('AppConfig.system.public_path'));
            });
        }
        
        //bind interface global
        foreach(config('AppConfig.binding.interface',[]) as $contract => $service){
            $this->app->bind(
                $contract,
                $service
            );
        }
        
        //bind controller rebind global
        foreach(config('AppConfig.binding.controller',[]) as $controller => $newController){
            $this->app->extend($controller, function ($service, $app) use ($newController) {
                return new $newController($service);
            });
        }

        // bind config binding per tenant
        // if(config('AppConfig.system.web_admin.multitenant.active')){
            $this->app->bind('bindTenant', function ($app,$params) {           
                
                //bind interface global
                foreach(config('AppConfig.system.binding.tenant.'.$params['tenant_id'].'.interface',[]) as $contract => $service){
                    $this->app->bind(
                        $contract,
                        $service
                    );
                }
                
                //bind controller rebind global
                foreach(config('AppConfig.system.binding.tenant.'.$params['tenant_id'].'.controller',[]) as $controller => $newController){
                    $this->app->extend($controller, function ($service, $app) use ($newController) {
                        return new $newController($service);
                    });
                }
            });
        // }

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}
