<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use App\Services\Utilities;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\TenantGroupTenant;

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

                if (!file_exists($path)) {
                    continue;
                }
                Route::middleware($isApi ? ['api'] : ['web'])
                    ->prefix($isApi && $moduleNamespace != 'moduser' ? config('AppConfig.endpoint.api.'.$moduleNamespace) : '')
                    ->namespace($namespace)
                    ->group($path);
            }
        });

        /*
        initiate language resource for vue apps
        get params : *optional
            lang : lang id nya
            item : item nya jika diperlukan
        */
        Route::get(config('AppConfig.system.lang_endpoint'), function (Request $request) use($config) {            
            if($request->input('lang')){
                app()->setLocale($request->input('lang'));
            }
            if($request->input('item')){
                return response()->json(trans($request->input('item')));
            }
            $lang = app()->getLocale();
            $trans = [];
            //get all language namespace
            foreach ($config['lang_path'] as $path) {
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

        
        /*
        initiate tenant app

        get params : *optional
            group_app : lang id nya

        return :
            tenant_list
            active_tenant
            active_tenant_group
        */
        Route::get(config('AppConfig.system.web_admin.multitenant.api_endpoint'), function (Request $request) { 

            $tenant = ['tenant_list'=>'','active_tenant'=>false,'active_tenant_group'=>false];
            if($request->input('group_app')){
                $tenant['active_tenant'] = Tenant::where('group_app',$request->input('group_app'))->first();
                if($tenant['active_tenant']){
                    $tenant['active_tenant_group'] = TenantGroupTenant::where('tenant_id',$tenant['active_tenant']->id)->get()->pluck('id');
                    if($tenant['active_tenant_group']->count()<=0) $tenant['active_tenant_group'] = false;
                }else{
                    $tenant['active_tenant'] = false;
                }
            }

            $tenant['tenant_list'] = Tenant::all();           
            
            return response()->json($tenant);
        });

        /*
        initiate route untuk Admin area Vue Frontend
        */
        //jika route admin autoload, maka langsung load
        // if(config('AppConfig.system.web_admin.autoload_router.backend')){
            $adminEndpoint = config('AppConfig.client.endpoint.'.config('AppConfig.system.mode').'.admin');
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
        // }

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
