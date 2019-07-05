<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use App\Services\Utilities;
use Illuminate\Http\Request;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    // protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootMigration();
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
            $pathToModule .= DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'migrations';
            if(file_exists($pathToModule)){
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
        $config = $this->app['config']['hpsynapse'];
        $middleware = $config['protection_middleware'];
        
        if(isset($config['protection_middleware'])){
            $middleware = array_merge($middleware,$config['protection_middleware']);
        }
        /*
        language resource for vue apps
        get params : *optional
            lang : lang id nya
            item : item nya jika diperlukan
        */
        Route::get(config('appconfig.system.lang_endpoint'), function (Request $request) {            
            if($request->input('lang')){
                app()->setLocale($request->input('lang'));
            }
            if($request->input('item')){
                return response()->json(trans($request->input('item')));
            }
            $lang = app()->getLocale();
            $trans = [];
            //get all language namespace
            foreach ($GLOBALS['LANG_PATH'] as $path) {
                $langItem = glob($path.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.'*');                
                foreach($langItem as $langFile){
                    $filename = basename($langFile, ".php");
                    if(!isset($trans[$filename])){
                        $trans[$filename] = trans($filename);
                        if(!is_array($trans[$filename]))unset($trans[$filename]);
                    }
                }
            }
            return response()->json($trans);
        });

        //jika route admin autoload, maka langsung load
        if(config('appconfig.system.web_admin.autoload_router.backend')){
            $adminEndpoint = config('appconfig.client.endpoint.'.config('appconfig.system.mode').'.admin');
            if($adminEndpoint!='/' && !empty($adminEndpoint)){
                Route::get($adminEndpoint, function(){
                    return view('layouts.admin.main');
                });
            }else{
                $adminEndpoint = '';
            }
            Route::get($adminEndpoint.'{any}', function(){
                return view('layouts.admin.main');
            })->where('any', '.*');
        }

        Utilities::listModulePath($config['namespaces'], function($namespace,$pathToModule) use ($middleware){
            
            $fileNames = [
                'routes_api' => true,
                'routes' => false
            ];
            $namespace .= 'Controllers';
            
            //load seluruh routes yg ada di setiap module
            foreach ($fileNames as $fileName => $isApi) {
                $path = sprintf('%s/%s.php', $pathToModule, $fileName);

                if (!file_exists($path)) {
                    continue;
                }

                Route::middleware($isApi ? ['api'] : ['web'])
                    ->namespace($namespace)
                    ->group($path);
            }
        });

        // $this->mapApiRoutes();
        // $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    // protected function mapWebRoutes()
    // {
    //     Route::middleware('web')
    //          ->namespace($this->namespace)
    //          ->group(base_path('routes/web.php'));
    // }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    // protected function mapApiRoutes()
    // {
    //     Route::prefix('api')
    //          ->middleware('api')
    //          ->namespace($this->namespace)
    //          ->group(base_path('routes/api.php'));
    // }
}
