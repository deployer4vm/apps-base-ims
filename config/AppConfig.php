<?php
require app_path('Helpers/Helper.php');

/**
 * Config utama yang menyimpan semua config aplikasi. Datanya disimpan di app/Module/System/config
 */
$client = json_decode(file_get_contents(__DIR__ . '/../app/MainApp/config/client.json'), true);
$listener = json_decode(file_get_contents(__DIR__ . '/../app/MainApp/config/listener.json'), true);

$keyConfig = json_decode(file_get_contents(__DIR__ . '/../resources/assets/src/config.json'), true);

/*
Load config system
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

/*
Proses package & packageLocal config.
merge config package & packageLocal menjadi packageLocal, karena package akan digunakan untuk default config package (module ataupun lib)
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

if(file_exists(__DIR__ . '/../app/MainApp/config/packageLocalEnv.json')){
    $tmpPackageLocalEnv = json_decode(file_get_contents(__DIR__ . '/../app/MainApp/config/packageLocalEnv.json'), true);
}else{
    file_put_contents(__DIR__ . '/../app/MainApp/config/packageLocalEnv.json', json_encode($tmpPackageLocal, JSON_PRETTY_PRINT));
    $tmpPackageLocalEnv = $tmpPackageLocal;
}

//initiate config ednpoint.json
$endpoint = [
    'domain' => $client['endpoint'][$system['mode']]['domain'],
    'admin' => [
        'app' => $client['endpoint'][$system['mode']]['admin'],
        'auth' => ''
    ],
    'frontend' => [
        'app' => $client['endpoint'][$system['mode']]['frontend'],
        'auth' => ''
    ],
    'api' => [
        'app' => $client['endpoint'][$system['mode']]['api'],
        'auth' => ''
    ],
    
];
$packageLocal = []; //untuk di load di config
$newPackageLocal = []; //untuk filtered packageLocal.json yang akan disave ulang
$newPackageLocalEnv = []; //untuk filtered packageLocalEnv.json yang akan disave ulang

foreach ($package as $item) {
    
    $newPackageLocal[$item['package_namespace']] =
        isset($tmpPackageLocal[$item['package_namespace']])
        ? $tmpPackageLocal[$item['package_namespace']]
        : [];
    $newPackageLocalEnv[$item['package_namespace']] =
        isset($tmpPackageLocalEnv[$item['package_namespace']])
        ? $tmpPackageLocalEnv[$item['package_namespace']]
        : [];

    //hapus package key config yang tidak boleh diedit
    foreach ($keyConfig['protected_packageLocal_key'] as $value) {
        if(isset($newPackageLocal[$item['package_namespace']][$value]))
            unset($newPackageLocal[$item['package_namespace']][$value]);
        if(isset($newPackageLocalEnv[$item['package_namespace']][$value]))
            unset($newPackageLocalEnv[$item['package_namespace']][$value]);
    }
    
    $packageLocal[$item['package_namespace']] = recuresive_array_merge($item, $newPackageLocal[$item['package_namespace']]);
    if ($system['mode'] == 'dev') {
        $packageLocal[$item['package_namespace']] = recuresive_array_merge(
            $packageLocal[$item['package_namespace']],
            $newPackageLocalEnv[$item['package_namespace']]
        );
    }
    
}

foreach ($package as $item) {
    /*
    generate endpoint masing-masing module
    */
    $moduleEndpoints = $packageLocal[$item['package_namespace']]['endpoint'][$system['mode']];
    foreach ($moduleEndpoints as $app => $moduleEndpoint) {        
        //jika module endpoint diawal "/" berarti tidak menggunakan apps endpoint
        if($moduleEndpoint == '' || $moduleEndpoint[0]!='/'){
            $endpoint[$app][$item['package_namespace']] = $endpoint[$app]['app'].'/'.$moduleEndpoint;
        }else{
            $endpoint[$app][$item['package_namespace']] = $moduleEndpoint;
        }    
        //jika memiliki fitur auth dan module user maka assign auth endpointnya
        if($system['has_auth'] && isset($packageLocal['moduser']) && $packageLocal['moduser']['enable']){
            $authEndpoint = $packageLocal['moduser']['auth_endpoint'][$system['mode']];            
            if($authEndpoint[0]!='/'){
                $endpoint[$app]['auth'] = $endpoint[$app]['app'].'/'.$authEndpoint;
            }else{
                $endpoint[$app]['auth'] = $authEndpoint;
            }
            
        }
    }
}

$newPackageLocalString = json_encode($newPackageLocal, JSON_PRETTY_PRINT);
//save ulang pakcageLocal hanya jika ada perubahan
if($packageLocalString != $newPackageLocalString){
    file_put_contents(__DIR__ . '/../app/MainApp/config/packageLocal.json', $newPackageLocalString);
}
file_put_contents(__DIR__ . '/../app/MainApp/config/packageLocalEnv.json', json_encode($newPackageLocalEnv, JSON_PRETTY_PRINT));
file_put_contents(__DIR__ . '/../app/MainApp/config/endpoint.json', json_encode($endpoint, JSON_PRETTY_PRINT));

//---generated config
file_put_contents(__DIR__ . '/../app/MainApp/config/_packageLocal.json', json_encode($packageLocal, JSON_PRETTY_PRINT));
file_put_contents(__DIR__ . '/../app/MainApp/config/_system.json', json_encode($system, JSON_PRETTY_PRINT));

/*
package dan module berisi config yang sama persis
*/
return [
    'client' => $client,
    'system' => $system,
    'endpoint' => $endpoint,
    'packageLocal' => $packageLocal, //config2 dari module dan lib yang sudah diedit per project
    'package' => $package, //config2 default dari module dan lib
    'listener' => $listener
];
