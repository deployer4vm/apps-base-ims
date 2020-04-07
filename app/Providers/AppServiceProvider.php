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
        
        //bind class rebind global
        foreach(config('AppConfig.binding.class',[]) as $class => $newClass){
            // $this->app->extend($class, function ($service, $app) use ($newClass) {
            //     return new $newClass($service);
            // });
            $this->app->bind($class, function ($app,$args) use ($newClass) {
                if(empty($args))return new $newClass();
                return new $newClass(...$args);
            });
        }

        // bind config binding per tenant
        if(config('AppConfig.system.web_admin.multitenant.active'))$this->bindTenant();

    }

    protected function bindTenant()
    {
        $this->app->bind('bindTenant', function ($app,$params) {           
                
            //bind interface global
            foreach(config('AppConfig.system.binding.tenant.'.$params['tenant_id'].'.interface',[]) as $contract => $service){
                $this->app->bind(
                    $contract,
                    $service
                );
            }
            
            //bind class rebind global
            foreach(config('AppConfig.system.binding.tenant.'.$params['tenant_id'].'.class',[]) as $controller => $newClass){
                // $this->app->extend($class, function ($service, $app) use ($newClass) {
                //     return new $newClass($service);
                // });
                $this->app->bind($class, function ($app,$args) use ($newClass) {
                    if(empty($args))return new $newClass();
                    return new $newClass(...$args);
                });
            }
        });
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
