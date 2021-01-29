<?php
require app_path('Helpers/Helper.php');

/**
 * Config utama yang menyimpan semua config aplikasi. Datanya disimpan di app/MainApp/config
 */

$client = json_decode(file_get_contents(__DIR__ . '/../app/MainApp/config/client.json'), true);

if(file_exists(__DIR__ . '/../app/MainApp/config/clientEnv.json')){
    $tmpEnvClient = json_decode(file_get_contents(__DIR__ . '/../app/MainApp/config/clientEnv.json'), true);
}else{
    file_put_contents(__DIR__ . '/../app/MainApp/config/clientEnv.json', json_encode($client, JSON_PRETTY_PRINT));
    $tmpEnvClient = $client;
}
//save ulang config pastikan tidak mengandung key yang tidak boleh diedit
$client = recuresive_array_merge($client, $tmpEnvClient);

//load config listener.json jika ada
// $listener = [];
// if(file_exists(__DIR__ . '/../app/MainApp/config/listener.json')){
//     $listener = json_decode(file_get_contents(__DIR__ . '/../app/MainApp/config/listener.json'), true);
// }

$keyConfig = json_decode(file_get_contents(__DIR__ . '/../resources/assets/src/config.json'), true);

/**
 * Load config system.json
 * ---------------------------------------------------------------------------------
 */
$system = json_decode(file_get_contents(__DIR__ . '/../app/MainApp/config/system.json'), true);

if(file_exists(__DIR__ . '/../app/MainApp/config/systemEnv.json')){
    $tmpEnvSystem = json_decode(file_get_contents(__DIR__ . '/../app/MainApp/config/systemEnv.json'), true);
}else{
    file_put_contents(__DIR__ . '/../app/MainApp/config/systemEnv.json', json_encode($system, JSON_PRETTY_PRINT));
    $tmpEnvSystem = $system;
}

$newEnv = [];
//hanya load systemEnv yang boleh dieditnya saja
foreach ($keyConfig['allowed_systemEnv_key'] as $value) {
    if (isset($tmpEnvSystem[$value])) {
        $newEnv[$value] = $tmpEnvSystem[$value];
    }
}
if (count($newEnv) >= 1) {
    //save ulang config pastikan tidak mengandung key yang tidak boleh diedit
    file_put_contents(__DIR__ . '/../app/MainApp/config/systemEnv.json', json_encode($newEnv, JSON_PRETTY_PRINT));
    $system = recuresive_array_merge($system, $newEnv);
}

/**
 * Load config system.json project yg aktif (multiproject), jika active dan ada
 * lalu mergekan dengan system.json utama
 * ---------------------------------------------------------------------------------
 */
if(isset($system['multiproject']['active']) && $system['multiproject']['active']==1 && file_exists(__DIR__ . '/../app/MainApp/Projects/'.$client['project_code'].'/config/system.json')){
    $perProjectSystem = json_decode(file_get_contents(__DIR__ . '/../app/MainApp/Projects/'.$client['project_code'].'/config/system.json'), true);
    
    $newEnv = [];
    //hanya load systemEnv yang boleh dieditnya saja
    foreach ($keyConfig['allowed_systemEnv_key'] as $value) {
        if (isset($perProjectSystem[$value])) {
            $newEnv[$value] = $perProjectSystem[$value];
        }
    }
    if (count($newEnv) >= 1) {
        //save ulang config pastikan tidak mengandung key yang tidak boleh diedit
        file_put_contents(__DIR__ . '/../app/MainApp/Projects/'.$client['project_code'].'/config/system.json', json_encode($newEnv, JSON_PRETTY_PRINT));
        $system = recuresive_array_merge($system, $newEnv);
    }
}

$multiTenantVuePrefix = isset($system['multitenant']['active'])&&$system['multitenant']['active']?'/:group_app':'';
$multiTenantLaravelPrefix = isset($system['multitenant']['active'])&&$system['multitenant']['active']?'/{group_app}':'';

/**
 * Proses package & packageLocal config.
 * merge config package & packageLocal menjadi packageLocal, karena package akan digunakan untuk default config package (module ataupun lib)
 * -------------------------------------------------------------------------------------------------------------------------------------------
 */

/*
load config module & lib
*/
$moduleList = array_merge(
    glob(base_path('app/MainApp/Modules/*/packageconfig.json')),
    glob(base_path('vendor/hp-synapse/*/packageconfig.json'))
);
$package = [];
foreach ($moduleList as $path) {
    $tmpPackage = json_decode(file_get_contents($path), true);
    $package[$tmpPackage['package_namespace']] = $tmpPackage;
}

$packageOld = '';
if(file_exists(__DIR__ . '/../app/MainApp/config/package.json')){
    $packageOld = file_get_contents(__DIR__ . '/../app/MainApp/config/package.json');
}

$packageText = json_encode($package, JSON_PRETTY_PRINT);

//save hanya jika ada perubahan
if($packageOld != $packageText) {
    file_put_contents(__DIR__ . '/../app/MainApp/config/package.json', $packageText);
}

//merge config package dengan package local
$packageLocalString = '';
if(file_exists(__DIR__ . '/../app/MainApp/config/packageLocal.json')){
    $packageLocalString = file_get_contents(__DIR__ . '/../app/MainApp/config/packageLocal.json');
    $tmpPackageLocal = json_decode($packageLocalString, true);
}else{
    $tmpPackageLocal = [];
}

/**
 * Load packageLocalEnv.json
 */
if(file_exists(__DIR__ . '/../app/MainApp/config/packageLocalEnv.json')){
    $tmpPackageLocalEnv = json_decode(file_get_contents(__DIR__ . '/../app/MainApp/config/packageLocalEnv.json'), true);
}else{
    file_put_contents(__DIR__ . '/../app/MainApp/config/packageLocalEnv.json', json_encode($tmpPackageLocal, JSON_PRETTY_PRINT));
    $tmpPackageLocalEnv = $tmpPackageLocal;
}


/**
 * Load config packageLocal.json project yg aktif (multiproject), jika active dan ada
 * untuk di mergekan dengan packageLocal.json utama
 * ---------------------------------------------------------------------------------
 */
$tmpPackageLocalPerProjectEnv = [];
if(isset($system['multiproject']['active']) && $system['multiproject']['active']==1 && file_exists(__DIR__ . '/../app/MainApp/Projects/'.$client['project_code'].'/config/packageLocal.json')){
    $tmpPackageLocalPerProjectEnv = json_decode(file_get_contents(__DIR__ . '/../app/MainApp/Projects/'.$client['project_code'].'/config/packageLocal.json'), true);
}

$homeSlug = isset($client['endpoint'][$system['mode']]['home_slug'])?$client['endpoint'][$system['mode']]['home_slug']:'';
$homeSlug = $homeSlug?('/'.trim($homeSlug,'/')):'';

//initiate config ednpoint.json
$endpoint = [
    'domain' => $client['endpoint'][$system['mode']]['domain'],
    'admin' => [
        'app' => $homeSlug.$multiTenantVuePrefix.$client['endpoint'][$system['mode']]['admin'],
        'auth' => $homeSlug.$multiTenantVuePrefix
    ],
    'frontend' => [
        'app' => $homeSlug.$multiTenantVuePrefix.$client['endpoint'][$system['mode']]['frontend'],
        'auth' => $homeSlug.$multiTenantVuePrefix
    ],
    'api' => [
        'app' => $homeSlug.$client['endpoint'][$system['mode']]['api'],
        'auth' => $homeSlug
    ],
    'laravel'  => [        
        'admin' => [
            'app' => $multiTenantLaravelPrefix.$client['endpoint'][$system['mode']]['admin'],
            'auth' => $multiTenantLaravelPrefix
        ],
        'frontend' => [
            'app' => $multiTenantLaravelPrefix.$client['endpoint'][$system['mode']]['frontend'],
            'auth' => $multiTenantLaravelPrefix
        ],
        'api' => [
            'app' => $client['endpoint'][$system['mode']]['api'],
            'auth' => ''
        ]
    ]   
];
$newPackageLocal = []; //untuk filtered packageLocal.json yang akan disave ulang
$newPackageLocalEnv = []; //untuk filtered packageLocalEnv.json yang akan disave ulang
$packageLocal = []; //pakcageLocal akhir setelah proses filtering & merging, dan disave ke MainApp/config/_packageLocal.env dan diload sebagai config utama

$acl = [];
$tmpSidenav = [];
$tmpSidenavNoPos = [];//package yg tidak diset access.pos nya
$sidenav = [];

/**
 * Filter acl
 */
if (!function_exists('processAcl')) {
    function processAcl($acl,$packageName,$aclPrefix,$children){
        foreach ($children as $aclId => $value) {
            if($value['enable'] && $value['acl_config']['show']){
                $aclPrefixTmp = $aclPrefix.'.'.$aclId;

                $acl[$packageName]['children'][$aclPrefixTmp] = $value['acl_config'];
                $acl[$packageName]['children'][$aclPrefixTmp]['parent'] = $aclPrefix;
                $acl[$packageName]['children'][$aclPrefixTmp]['tenant_group_id'] = isset($value['tenant_group_id'])?$value['tenant_group_id']:0;
                $acl[$packageName]['children'][$aclPrefixTmp]['acl_caption'] = isset($value['acl_caption'])?$value['acl_caption']:$value['caption'];
                $acl[$packageName]['children'][$aclPrefixTmp]['acl_description'] = isset($value['acl_description'])?$value['acl_description']:$value['description'];

                //jika masih ada child nya proses terus
                if(isset($value['children'])){
                    unset($acl[$aclPrefixTmp]['children']);
                    $acl = processAcl($acl,$packageName,$aclPrefixTmp,$value['children']);
                }
            }
        }
        return $acl;
    }
}

/**
 * Generate sidenav
 */
if (!function_exists('processSidenav')) {
    function processSidenav($children){
        $res = [];
        foreach ($children as $aclId => $value) {
            if($value['enable'] && $value['is_navbar']){
                $res[$aclId] = $value;
                //jika masih ada child nya proses terus
                if(isset($value['children'])){
                    $res[$aclId]['children'] = processSidenav($res[$aclId]['children']);
                    if(count($res[$aclId]['children'])==0)
                        unset($res[$aclId]['children']);
                }
            }
        }
        return $res;
    }
}

// looping semua packageconfig yg ada untuk proses filtering dan pemrosesan yang menghasilkan _packageLocal.json, _sidenav.json dan _acl.json di MainApp/config
foreach ($package as $item) {
    
    // get data packageconfig dari packageLocal.json di MainApp/config untuk diproses selanjutnya
    $newPackageLocal[$item['package_namespace']] =
        isset($tmpPackageLocal[$item['package_namespace']])
        ? $tmpPackageLocal[$item['package_namespace']]
        : [];

    // get data packageconfig dari packageLocalEnv.json di MainApp/config untuk diproses selanjutnya
    $newPackageLocalEnv[$item['package_namespace']] =
        isset($tmpPackageLocalEnv[$item['package_namespace']])
        ? $tmpPackageLocalEnv[$item['package_namespace']]
        : [];

    //hapus key packageconfig.json yang tidak boleh diedit, baik yg dari packageLocal.json maupun packageLocalEnv.json
    foreach ($keyConfig['protected_packageLocal_key'] as $value) {
        if(isset($newPackageLocal[$item['package_namespace']][$value]))
            unset($newPackageLocal[$item['package_namespace']][$value]);
        if(isset($newPackageLocalEnv[$item['package_namespace']][$value]))
            unset($newPackageLocalEnv[$item['package_namespace']][$value]);
    }
    
    // merge packageconfig asli dari masing-masing module dengan packageconfig dari packageLocal.json di MainApp/config
    $packageLocal[$item['package_namespace']] = recuresive_array_merge($item, $newPackageLocal[$item['package_namespace']]);

    
    // merge packageconfig sebelumnya (hasil merge) dengan packageconfig dari packageLocalEnv.json di MainApp/config
    // if ($system['mode'] == 'dev') {
        $packageLocal[$item['package_namespace']] = recuresive_array_merge(
            $packageLocal[$item['package_namespace']],
            $newPackageLocalEnv[$item['package_namespace']]
        );
    // }    
    
    // merge packageconfig sebelumnya (hasil merge) dengan packageconfig dari packageLocal.json di Mmasing-masing config project (jika project multi project)    
    $packageLocal[$item['package_namespace']] = recuresive_array_merge(
        $packageLocal[$item['package_namespace']],
        $tmpPackageLocalPerProjectEnv
    );

    /**
     * proses generate _acl.json dan _sidenav.json
     */
    if($packageLocal[$item['package_namespace']]['enable']){
        //proses _acl.json
        if($packageLocal[$item['package_namespace']]['access']['has_acl']){
            $acl[$item['package_namespace']] = [
                'acl_caption' => 
                    isset($packageLocal[$item['package_namespace']]['access']['acl_caption'])?
                    $packageLocal[$item['package_namespace']]['access']['acl_caption']:
                    $packageLocal[$item['package_namespace']]['access']['caption'],
                'acl_description' => 
                    isset($packageLocal[$item['package_namespace']]['access']['acl_description'])?
                    $packageLocal[$item['package_namespace']]['access']['acl_description']:
                    $packageLocal[$item['package_namespace']]['access']['description'],
                'tenant_group_id' =>
                    isset($packageLocal[$item['package_namespace']]['access']['tenant_group_id'])?
                    $packageLocal[$item['package_namespace']]['access']['tenant_group_id']:0,
            ];
            if(isset($packageLocal[$item['package_namespace']]['access']['children'])){
                $acl = processAcl($acl,$item['package_namespace'],$item['package_namespace'],$packageLocal[$item['package_namespace']]['access']['children']);
            }
        }

        //proses _sidenav
        if($packageLocal[$item['package_namespace']]['access']['is_navbar']){
            $tmpSidenavTmp = ['package_namespace'=>$item['package_namespace'],$item['package_namespace'] => $packageLocal[$item['package_namespace']]['access']];
            
            if(isset($tmpSidenavTmp[$item['package_namespace']]['children'])){
                $tmpSidenavTmp[$item['package_namespace']]['children'] = processSidenav($tmpSidenavTmp[$item['package_namespace']]['children']);
                if(count($tmpSidenavTmp[$item['package_namespace']]['children'])==0)
                    unset($tmpSidenavTmp[$item['package_namespace']]['children']);
            }    
            
            if(isset($packageLocal[$item['package_namespace']]['access']['position'])){
                $tmpSidenav[ $packageLocal[$item['package_namespace']]['access']['position'] ] = $tmpSidenavTmp;
            }else{
                $tmpSidenavNoPos[] = $tmpSidenavTmp;
            }
        }
    }
    
}

foreach ($tmpSidenavNoPos as $value) {
    $tmpSidenav[] = $value;
}
ksort($tmpSidenav);
foreach ($tmpSidenav as $key => $value) {
    $sidenav[$value['package_namespace']] = $value[$value['package_namespace']];
}


$filePath = "";//path real saat build
$packagePath = "";//path untuk load selain main.js
$packageMainPath = "";//path untuk load main.js

$pathToBase = str_replace('\\', '/',base_path(''));

$moduleMainJs = [
    "// DO NOT EDIT MANUALY UNLESS YOU KNOW WHAT YOU ARE DOING \n" .
    "// This files is autogenerated on build and by app-generator \n" .
    "// containt list all main.js for every module registered to this project \n\n"
];
//---
$moduleStore = [
    "// DO NOT EDIT MANUALY UNLESS YOU KNOW WHAT YOU ARE DOING \n" .
    "// This files is autogenerated on build and by app-generator \n" .
    "// load all vuex state for every module registered to this project \n\n"
];
$moduleStoreNamespace = [];
//---
$moduleRouter = [
    "// DO NOT EDIT MANUALY UNLESS YOU KNOW WHAT YOU ARE DOING \n" .
    "// This files is autogenerated on build and by app-generator \n" .
    "// load all router for every module registered to this project \n\n"
];
$moduleRouterNamespace = [];
//---
$moduleRouterAdmin = [
    "// DO NOT EDIT MANUALY UNLESS YOU KNOW WHAT YOU ARE DOING \n" .
    "// This files is autogenerated on build and by app-generator \n" .
    "// load all router admin endpoint for every module registered to this project \n\n"
];
$moduleRouterAdminNamespace = [];

$hpsynapse = include(__DIR__.DIRECTORY_SEPARATOR.'hpsynapse.php');

$binding = empty($hpsynapse['bindings'])?[
    'class'=>[],
    'interface'=>[],
    'route'=>[],
    'alias'=>[]
]:$hpsynapse['bindings'];
$providers = [];

foreach ($packageLocal as $item) {
    /*
    generate binding masing-masing module
    ----------------------------
    */
    //binding interface
    if(isset($item['binding']) && isset($item['binding']['interface'])){
        foreach($item['binding']['interface'] as $contract => $service){
            $binding['interface'][$contract] = $service;
        }        
    }

    
    //provider per module
    if(isset($item['providers']) && $item['is_package']==0){
        foreach($item['providers']as $provider){
            $providers[] = $provider;
        }        
    }

    /*
    generate endpoint masing-masing module
    ----------------------------
    */
    $moduleEndpoints = $packageLocal[$item['package_namespace']]['endpoint'][$system['mode']];
    foreach ($moduleEndpoints as $app => $moduleEndpoint) {        
        //jika module endpoint diawal "/" berarti tidak menggunakan apps endpoint
        if($moduleEndpoint == '' || $moduleEndpoint[0]!='/'){
            $endpoint[$app][$item['package_namespace']] = $endpoint[$app]['app'].'/'.$moduleEndpoint;
            $endpoint['laravel'][$app][$item['package_namespace']] = $endpoint['laravel'][$app]['app'].'/'.$moduleEndpoint;
        }else{
            $endpoint[$app][$item['package_namespace']] = $homeSlug.$multiTenantVuePrefix.$moduleEndpoint;
            $endpoint['laravel'][$app][$item['package_namespace']] = $multiTenantLaravelPrefix.$moduleEndpoint;
        }    
        //jika memiliki fitur auth dan module user maka assign auth endpointnya
        if($system['has_auth'] && isset($packageLocal['moduser']) && $packageLocal['moduser']['enable']){
            $authEndpoint = $packageLocal['moduser']['auth_endpoint'][$system['mode']];            
            if($authEndpoint[0]!='/'){
                $endpoint[$app]['auth'] = $endpoint[$app]['app'].'/'.$authEndpoint;
                $endpoint['laravel'][$app]['auth'] = $endpoint['laravel'][$app]['app'].'/'.$authEndpoint;
            }else{
                $endpoint[$app]['auth'] = $homeSlug.$multiTenantVuePrefix.$authEndpoint;
                $endpoint['laravel'][$app]['auth'] = $multiTenantLaravelPrefix.$authEndpoint;
            }
            
        }
    }

    /*
    generate loader store, router, routerAdmin dan init.js untuk package
    -------------------------
    */
    if($item['is_package']){
        $filePath = "vendor/hp-synapse/".$item['package_dir']."/src/";
        $packagePath = $pathToBase."/vendor/hp-synapse/".$item['package_dir']."/src/";
        $packageMainPath = $pathToBase."/vendor/hp-synapse/".$item['package_dir']."/src/";
    }else{
        $filePath = "app/MainApp/Modules/".$item['package_dir']."/";
        $packagePath = "../../../Modules/".$item['package_dir']."/";
        $packageMainPath = "../../Modules/".$item['package_dir']."/";
    }
    
    //load package hanya jika aktif saja
    if($packageLocal[$item['package_namespace']]['enable'] ){
        //untuk loader main.js
        if (file_exists($filePath . "resources/js/main.js")) {
            $moduleMainJs[] = 'require("' . $packageMainPath . 'resources/js/main");' . "\n";
        }
        
        //untuk loader vuex store
        if (file_exists($filePath."resources/js/store/store.js")) {
            $moduleStore[] = "import ".$item['package_namespace'].' from "'.$packagePath.'resources/js/store/store";' . "\n";
            $moduleStoreNamespace[] = "    ..." . $item['package_namespace'];
        }

        //untuk loader vue router
        if (file_exists($filePath."resources/js/router/index.js")) {
            $moduleRouter[] = "import " . $item['package_namespace'] . ' from "' . $packagePath . 'resources/js/router/index";' . "\n";
            $moduleRouterNamespace[] = "    .concat(" . $item['package_namespace'] . ")";
        }

        //untuk loader vue router admin endpoint
        if (file_exists($filePath."resources/js/router/indexAdmin.js")) {
            $moduleRouterAdmin[] = "import " . $item['package_namespace'] . ' from "' . $packagePath . 'resources/js/router/indexAdmin";' . "\n";
            $moduleRouterAdminNamespace[] = "    .concat(" . $item['package_namespace'] . ")";
        }
    }

}

$newPackageLocalString = json_encode($newPackageLocal, JSON_PRETTY_PRINT);
//save ulang pakcageLocal hanya jika ada perubahan
if($packageLocalString != $newPackageLocalString){
    file_put_contents(__DIR__ . '/../app/MainApp/config/packageLocal.json', $newPackageLocalString);
}
file_put_contents(__DIR__ . '/../app/MainApp/config/packageLocalEnv.json', json_encode($newPackageLocalEnv, JSON_PRETTY_PRINT));
file_put_contents(__DIR__ . '/../app/MainApp/config/_endpoint.json', json_encode($endpoint, JSON_PRETTY_PRINT));

$system['path'] = [
    'MainApp'=>app_path('MainApp'),
    'basePath'=>base_path('')
];

//---merge binding per module dengan binding utama (system)
//binding interface
if(isset($system['binding']['interface'])){
    foreach($system['binding']['interface'] as $contract => $service){
        if(!isset($binding['interface'][$contract]))
            $binding['interface'][$contract] = $service;
    }        
}
//binding class
if(isset($system['binding']['class'])){
    foreach($system['binding']['class'] as $contract => $service){
        if(!isset($binding['class'][$contract]))
            $binding['class'][$contract] = $service;
    }        
}
//binding route
if(isset($system['binding']['route'])){
    foreach($system['binding']['route'] as $contract => $service){
        if(!isset($binding['route'][$contract]))
            $binding['route'][$contract] = $service;
    }        
}
//binding alias
if(isset($system['binding']['alias'])){
    foreach($system['binding']['alias'] as $contract => $service){
        if(!isset($binding['alias'][$contract]))
            $binding['alias'][$contract] = $service;
    }        
}

//merge providers
if(!empty($providers)){
    if(!isset($system['providers'])) $system['providers'] = [];
    $system['providers'] = array_merge($system['providers'],$providers);
}

//generate modulesMultitenant.js
$tenantList = json_decode(file_get_contents(__DIR__ . '/../app/MainApp/config/_tenant.json'), true);
$moduleMultitenant = [
    "// DO NOT EDIT MANUALY UNLESS YOU KNOW WHAT YOU ARE DOING \n" .
    "// This files is autogenerated on build and by app-generator \n" .
    "// load all multitenant component & function from all tenant multitenant.js \n\n"
];
$moduleMultitenantItem = [];
if(isset($system['multitenant']['active']) && $system['multitenant']['active'])
    foreach ($tenantList as $tenantId) {
        //untuk loader multitenant component registration
        if (file_exists('app/MainApp/Projects/'.$client['project_code'].'/Tenants/ID'.$tenantId.'/resources/js/multitenant.js')) {
            $moduleMultitenant[] = 'import ID'.$tenantId.' from "@/../../../app/MainApp/Projects/'.$client['project_code'].'/Tenants/ID'.$tenantId.'/resources/js/multitenant";' . "\n";
            $moduleMultitenantItem[] = '    '.$tenantId.': ID'.$tenantId;
        }else if (file_exists('app/MainApp/Tenants/ID'.$tenantId.'/resources/js/multitenant.js')) {
            $moduleMultitenant[] = 'import ID'.$tenantId.' from "@/../../../app/MainApp/Tenants/ID'.$tenantId.'/resources/js/multitenant";' . "\n";
            $moduleMultitenantItem[] = '    '.$tenantId.': ID'.$tenantId;
        }
    }

//---generated config
file_put_contents(__DIR__ . '/../app/MainApp/resources/js/modules.js', implode('',$moduleMainJs));
file_put_contents(__DIR__ . '/../app/MainApp/resources/js/modulesMultitenant.js', implode('',$moduleMultitenant)."\nexport default {\n".implode(",\n",$moduleMultitenantItem)."\n};" );
file_put_contents(__DIR__ . '/../app/MainApp/resources/js/store/modules.js', implode('',$moduleStore)."\nconst store = {\n".implode(",\n",$moduleStoreNamespace)."\n};\n\nexport default store;" );
file_put_contents(__DIR__ . '/../app/MainApp/resources/js/router/modules.js', implode('',$moduleRouter)."\nconst routes = []\n".implode("\n",$moduleRouterNamespace).";\n\nexport default routes;" );
file_put_contents(__DIR__ . '/../app/MainApp/resources/js/router/modulesAdmin.js', implode('',$moduleRouterAdmin)."\nconst routes = []\n".implode("\n",$moduleRouterAdminNamespace).";\n\nexport default routes;" );

//---generated config
file_put_contents(__DIR__ . '/../app/MainApp/config/_binding.json', json_encode($binding, JSON_PRETTY_PRINT));
file_put_contents(__DIR__ . '/../app/MainApp/config/_packageLocal.json', json_encode($packageLocal, JSON_PRETTY_PRINT));
file_put_contents(__DIR__ . '/../app/MainApp/config/_system.json', json_encode($system, JSON_PRETTY_PRINT));
file_put_contents(__DIR__ . '/../app/MainApp/config/_client.json', json_encode($client, JSON_PRETTY_PRINT));
file_put_contents(__DIR__ . '/../app/MainApp/config/_acl.json', json_encode($acl, JSON_PRETTY_PRINT));
file_put_contents(__DIR__ . '/../app/MainApp/config/_sidenav.json', json_encode($sidenav, JSON_PRETTY_PRINT));

/*
package dan module berisi config yang sama persis
*/
return [
    'client' => $client,
    'system' => $system,
    'binding' => $binding,
    'endpoint' => $endpoint,
    'packageLocal' => $packageLocal, //config2 dari module dan lib yang sudah diedit per project
    'package' => $package, //config2 default dari module dan lib
    // 'listener' => $listener,
    'sidenav' => $sidenav,
    'tenant' => $tenantList, //_tenant.json , list tenant
    'acl' => $acl //_acl.json
];