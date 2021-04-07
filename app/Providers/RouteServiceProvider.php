<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Services\Utilities;
// use Illuminate\Http\Request;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        if($this->app->runningInConsole())
            $this->bootMigration();

        parent::boot();
    }

    /*
     * tambah migration path disetiap module
     */
    private function bootMigration()
    {
        //boot additional data type migration
        try {
            if (!\Doctrine\DBAL\Types\Type::hasType('double')) {
                \Doctrine\DBAL\Types\Type::addType('double', \App\Base\DoctrineType\DoubleType::class);
            }
            if (!\Doctrine\DBAL\Types\Type::hasType('tinyInteger')) {
                \Doctrine\DBAL\Types\Type::addType('tinyInteger', \App\Base\DoctrineType\TinyIntegerType::class);
            }
        } catch (\Throwable $th) {
        }
        
        $this->loadMigrationsFrom(config('hpsynapse.migration_path'));
    }

    /**
     * Register
     */
    public function register()
    {
        require_once app_path('Helpers/Helper.php');
        
        // $this->mergeConfigFrom(
        //     __DIR__.'/../config/HPSynapse.php', config_path('hpsynapse.php')
        // );      
        
        // $this->app->singleton('breadcrumb', function ($app) {
        //     return new \hpsynapse\appscore\Services\Breadcrumb();
        // });
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {        
        if(!$this->app->runningInConsole())
            $this->registerControllerNamespace();

    }

    /**
     * initiate route untuk Admin area Vue Frontend di web akses
     */
    protected function registerControllerNamespace()
    {
        $controllerPaths = config('hpsynapse.controller_path');
        foreach ($controllerPaths as $namespace => $pathToModule) {
            
            $fileNames = [
                'routes_api' => true,
                'routes' => false
            ];
            
            $moduleNamespace = explode("\\",trim($namespace,"\\"));
            $moduleNamespace = array_pop($moduleNamespace);

            $namespace .= 'Controllers';
            
            //load seluruh routes yg ada di setiap module
            foreach ($fileNames as $fileName => $isApi) {
                $path = sprintf('%s/%s.php', $pathToModule, $fileName);

                //load general route tambahan jika ada
                if($pathBinding = config('AppConfig.binding.route.'.$moduleNamespace.'.'.($isApi?'api':'web'),false)){
                    $pathBinding = app_path('MainApp' . DIRECTORY_SEPARATOR . $pathBinding);
                    if (file_exists($pathBinding)) {
                        Route::middleware($isApi ? ['api'] : ['web'])
                            ->prefix($isApi && $moduleNamespace != 'moduser' ? config('AppConfig.endpoint.laravel.api.'.$moduleNamespace) : '')
                            ->namespace($namespace)
                            ->group($pathBinding);
                    }
                }

                if (!file_exists($path)) {
                    continue;
                }
                
                // register router utama per module
                Route::middleware($isApi ? ['api'] : ['web'])
                    ->prefix($isApi && $moduleNamespace != 'moduser' ? config('AppConfig.endpoint.laravel.api.'.$moduleNamespace) : '')
                    ->namespace($namespace)
                    ->group($path);
            }
        }

        $this->mapApiRoutes();
        $this->mapWebRoutes();
        
        //jika full_vue aktif maka load route config nya
        if(config('AppConfig.system.web_admin.full_vue'))
            $this->mapWebFullVueRoutes();
    }

    /**
     * full vue web routes
     *
     * @return void
     */
    protected function mapWebFullVueRoutes()
    {        
        $adminEndpoint = config('AppConfig.endpoint.laravel.admin.app');
        if($adminEndpoint!='/' && !empty($adminEndpoint)){                
            Route::middleware('web')
                ->get($adminEndpoint, function(){
                    return view('layouts.full_vue.main');
                });
        }else{
            $adminEndpoint = '';
        }
        Route::middleware('web')
            ->get($adminEndpoint.'{any}', function(){
                return view('layouts.full_vue.main');
            })->where('any', '.*');        
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix(config('AppConfig.endpoint.laravel.api.app'))
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }
}
