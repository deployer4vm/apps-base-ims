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
        if($this->app->runningInConsole())$this->bootMigration();
        parent::boot();
    }

    /*
     * tambah migration path disetiap module
     */
    private function bootMigration()
    {
        $mainPath = database_path('migrations'); 
        $appPath = app_path('MainApp'.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'migrations');       
        
        $modulePath = Utilities::listModulePath($this->app['config']['hpsynapse']['namespaces'], function($namespace,$pathToModule){            
            $moduleNamespace = explode('\\',trim($namespace,'\\'));
            $moduleNamespace = array_pop($moduleNamespace);            
            $pathToModule .= DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'migrations';
            if(config('AppConfig.packageLocal.'.$moduleNamespace.'.database.run_migration',true) && file_exists($pathToModule)){
                return $pathToModule;
            }
        });
        
        $paths = array_merge([$mainPath], $modulePath);
        $paths[] = $appPath;
        $this->loadMigrationsFrom($paths);
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
        
//        $this->app->singleton('breadcrumb', function ($app) {
//            return new \hpsynapse\appscore\Services\Breadcrumb();
//        });
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {        

        // $homeSlug = trim(config('AppConfig.client.endpoint.'.config('AppConfig.system.mode').'.home_slug',''),'/');
        // if($homeSlug) $homeSlug = '/'.$homeSlug;

        $config = $this->app['config']['hpsynapse'];
        // $middleware = $config['protection_middleware'];        
        // if(isset($config['protection_middleware'])){
        //     $middleware = array_merge($middleware,$config['protection_middleware']);
        // }
        Utilities::listModulePath($config['namespaces'], function($namespace,$pathToModule) {
            
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
                
                Route::middleware($isApi ? ['api'] : ['web'])
                    ->prefix($isApi && $moduleNamespace != 'moduser' ? config('AppConfig.endpoint.laravel.api.'.$moduleNamespace) : '')
                    ->namespace($namespace)
                    ->group($path);
            }
        });

        $this->mapApiRoutes();
        $this->mapWebRoutes();

        /**
         * initiate route untuk Admin area Vue Frontend
         */
        
        //jika full_vue aktif maka load route config nya
        if(config('AppConfig.system.web_admin.full_vue')){            
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
