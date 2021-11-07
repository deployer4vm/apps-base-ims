<?php
use App\Services\Utilities;

if(!function_exists('initHPsynapseConfig')){
    function initHPsynapseConfig(){
        $system = json_decode(file_get_contents(__DIR__ . '/../app/MainApp/config/_system.json'), true);
        $client = json_decode(file_get_contents(__DIR__ . '/../app/MainApp/config/_client.json'), true);

        $config = [
            'namespaces' => [
                'App\\MainApp\\Modules' => [app_path('MainApp' . DIRECTORY_SEPARATOR . 'Modules') . DIRECTORY_SEPARATOR, false],
                'hpsynapse' => [base_path('vendor' . DIRECTORY_SEPARATOR . 'hp-synapse') . DIRECTORY_SEPARATOR, ['mod-','apps-']]
            ],
            'language_folder_name' => 'lang',
            'resource_namespace' => 'resources',
            'view_folder_name' => 'views',
        ];

        // jika multi project, maka masukan namespace project active nya
        if(isset($system['multiproject']['active']) && $system['multiproject']['active']==1)
            $config['namespaces']['App\\MainApp\\Projects\\'.$client['project_code'].'\\Modules'] = [app_path('MainApp' . DIRECTORY_SEPARATOR . 'Projects' . DIRECTORY_SEPARATOR . $client['project_code'] . DIRECTORY_SEPARATOR . 'Modules') . DIRECTORY_SEPARATOR, false];

        if(isset($system['multitenant']['active']) && $system['multitenant']['active']==1){
            $tenantConfigPath = __DIR__ . '/../app/MainApp/config/_tenant.json';
            
            if(!file_exists($tenantConfigPath))                
                file_put_contents($tenantConfigPath, json_encode([], JSON_PRETTY_PRINT));

            $tenantList = json_decode(file_get_contents($tenantConfigPath), true);
            foreach ($tenantList as $tenantId) {
                if(isset($system['multiproject']['active']) && $system['multiproject']['active'] == 1){
                    $config['namespaces']['App\\MainApp\\Projects\\'.$client['project_code'].'\\Tenants\\ID'.$tenantId.'\\Modules'] = [app_path('MainApp' . DIRECTORY_SEPARATOR . 'Projects' . DIRECTORY_SEPARATOR . $client['project_code'] . DIRECTORY_SEPARATOR  . 'Tenants' . DIRECTORY_SEPARATOR . 'ID' . $tenantId . DIRECTORY_SEPARATOR . 'Modules') . DIRECTORY_SEPARATOR, false];
                }else{
                    $config['namespaces']['App\\MainApp\\Tenants\\ID'.$tenantId.'\\Modules'] = [app_path('MainApp' . DIRECTORY_SEPARATOR . 'Tenants' . DIRECTORY_SEPARATOR . 'ID' . $tenantId . DIRECTORY_SEPARATOR . 'Modules') . DIRECTORY_SEPARATOR, false];
                }
            }
        }

        /**
         * Load Controller path
         */
        $tmpControllerPath = Utilities::listModulePath($config['namespaces'], function($namespace,$pathToModule) {
            return [$namespace,$pathToModule];
        });
        $controllerPath=[];
        foreach ($tmpControllerPath as $key => $value) {
            $controllerPath[$value[0]] = $value[1];
        }

        /**
         * Load LANG path
         */
        $langPath = Utilities::findNamespaceResources(
            $config['namespaces'] ,
            $config['language_folder_name'],
            $config['resource_namespace']
        );

        $langPath = array_merge(
            [
                resource_path('lang')
            ], 
            $langPath
        );

        $langPath[] = app_path('MainApp' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'lang');

        /**
         * Load view path
         */
        $viewPath = Utilities::findNamespaceResources(
            $config['namespaces'], $config['view_folder_name'], $config['resource_namespace']
        );
        $viewPath[] = app_path('MainApp' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views');

        /**
         * Load migration path
         */
        $packageLocal = json_decode(file_get_contents(__DIR__ . '/../app/MainApp/config/_packageLocal.json'), true);
        $migrationModulePath = Utilities::listModulePath($config['namespaces'], function($namespace,$pathToModule) use ($packageLocal){            
            $moduleNamespace = explode('\\',trim($namespace,'\\'));
            $moduleNamespace = array_pop($moduleNamespace);            
            $pathToModule .= DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'migrations';
            if(
                (
                    !isset($packageLocal[$moduleNamespace]['database']['run_migration']) ||
                    (
                        isset($packageLocal[$moduleNamespace]['database']['run_migration']) && 
                        $packageLocal[$moduleNamespace]['database']['run_migration'] == 1
                    )
                ) && 
                file_exists($pathToModule)
            ){
                return $pathToModule;
            }
        });

        $mainPath = database_path('migrations');
        $migrationPath = array_merge([$mainPath], $migrationModulePath);

        $migrationPath[] = app_path('MainApp'.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'migrations'); 

        // jika multi project maka load juga migration project nya
        if(isset($system['multiproject']['active']) && $system['multiproject']['active']==1) 
            $migrationPath[] = app_path('MainApp'.DIRECTORY_SEPARATOR.'Project'.DIRECTORY_SEPARATOR.$client['project_code'].DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'migrations'); 

        // jika multi tenant maka load path lang per tenant
        if(isset($system['multitenant']['active']) && $system['multitenant']['active']==1){
            foreach ($tenantList as $tenantId) {
                if(isset($system['multiproject']['active']) && $system['multiproject']['active'] == 1){
                    $path = app_path('MainApp' . DIRECTORY_SEPARATOR . 'Projects' . DIRECTORY_SEPARATOR . $client['project_code'] . DIRECTORY_SEPARATOR  . 'Tenants' . DIRECTORY_SEPARATOR . 'ID' . $tenantId . DIRECTORY_SEPARATOR . 'database'.DIRECTORY_SEPARATOR.'migrations');
                    if(file_exists($path))
                        $migrationPath[] = $path;
                }else{
                    $path = app_path('MainApp' . DIRECTORY_SEPARATOR . 'Tenants' . DIRECTORY_SEPARATOR . 'ID' . $tenantId . DIRECTORY_SEPARATOR . 'database'.DIRECTORY_SEPARATOR.'migrations');
                    if(file_exists($path))
                        $migrationPath[] = $path;
                }
            }
        }
        
        return [
            'bindings' => [
                'controller'=>[],
                'interface'=>[
                    'App\\Contracts\\HybridAuth'=>'App\\Services\\HybridAuth',
                    'App\\Contracts\\Tenant'=>'App\\Services\\Tenant',
                    'App\\Contracts\\Excel'=>'App\\Services\\Excel',
                    'App\\Contracts\\Backup'=>'App\\Services\\Backup',
                    'App\\Contracts\\CacheConfig'=>'App\\Services\\CacheConfig',
                    'App\\Contracts\\DbConfig'=>'App\\Services\\DbConfig',
                    'App\\Contracts\\Helper'=>'App\\Services\\Helper',
                    'App\\Contracts\\Web'=>'App\\Services\\Web',
                    'App\\Contracts\\Trans'=>'App\\Services\\Trans',
                    'App\\Contracts\\Export'=>'App\\Services\\Export',
                    'App\\Contracts\\ExportSpout'=>'App\\Services\\ExportSpout'
                ],
                'route'=>[]
            ],
            'lang_path' => $langPath,//language path
            'controller_path' => $controllerPath,//controller path
            'view_path' => $viewPath,//blade view path
            'migration_path' => $migrationPath,//migration path
            /*
            * namespace ke path lokasi daftar module module
            *  NAMESPACE => [path_to_module_group, FILTER PREFIX
            */
            'namespaces' => $config['namespaces'],
            'lib_namespace' => ['hpsynapse' => [base_path('vendor' . DIRECTORY_SEPARATOR . 'hp-synapse'), 'lib-']],
            
            'resource_namespace' => $config['resource_namespace'],
            
            'language_folder_name' => $config['language_folder_name'],
            
            'view_folder_name' => $config['view_folder_name'],

            /*
            * dev_package_path : path ke package disimpan secara fisik saat development
            * relative ke base_path()
            */
            'dev_package_path' => '../',
            'protection_middleware' => [
                
            ],    
            /*
            * struktur table default yang akan digenerate jika tidak mencantumkan 
            * nama tabel saat generate
            */
            'generate_table_default' => [
                'name' => 'varchar',
                'description' => 'text'
            ],
            /*
            * field yang akan di hilangkan form dan list serta akan dimasukan
            * ke model guarded attribut
            */
            'generate_table_field_exclude' => [
                'id','created_at','updated_at'
            ],
            /*
            * template layout utama yg akan di extend saat generate module
            */
            'generate_default_layout' => 'layouts.app',
            /*
            * view dari sidebar menu yang akan ditambahkan menu baru oleh system
            */
            'generate_sidebar_layouts' => 'layouts.adminsidebar',
            /*
            * tag html container menu sidebar yg akan ditambah
            */
            'generate_sidebar_menu_tag' => 'ul',
            /*
            * zappid dari tag container menu sidebar yg akan ditambah
            */
            'generate_sidebar_menu_id' => 'menusidebar'
        ];

    }
}

return initHPsynapseConfig();