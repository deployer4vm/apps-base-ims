<?php
/**
 * Config utama yang menyimpan semua config aplikasi. Datanya disimpan di app/Module/System/config
 */
$client = json_decode(file_get_contents(__DIR__.'/../app/MainApp/config/client.json'),true);
$listener = json_decode(file_get_contents(__DIR__.'/../app/MainApp/config/listener.json'),true);

/*
Load config system
*/
$system = json_decode(file_get_contents(__DIR__.'/../app/MainApp/config/system.json'),true);
$tmpEnvSystem = json_decode(file_get_contents(__DIR__.'/../app/MainApp/config/systemEnv.json'),true);
if($tmpEnvSystem['mode']=='dev')
    $system = array_merge($system,$tmpEnvSystem);

/*
Proses package & packageLocal config.
merge config package & packageLocal menjadi packageLocal, karena package akan digunakan untuk default config package (module ataupun lib)
*/

/*
load config module & lib
*/
$package = json_decode(file_get_contents(__DIR__.'/../app/MainApp/config/package.json'),true);
// tidak jadi digenerate di sini, otomatis di generate saat build npm
// $tmpPackage = json_decode(file_get_contents(__DIR__.'/../app/MainApp/config/package.json'),true);
// foreach ($tmpPackage as $key => $value) {
//     if($value['is_package']){
//         $path = __DIR__.'/../vendor/hp-synapse/'.$value['package_dir'].'/packageconfig.json';
//     }else{
//         $path = __DIR__.'/../app/MainApp/Modules/'.$value['package_dir'].'/packageconfig.json';
//     }
//     $package[$value['package_namespace']] = json_decode(file_get_contents($path),true);
// }

//merge config package dengan package local
$tmpPackageLocal = json_decode(file_get_contents(__DIR__.'/../app/MainApp/config/packageLocal.json'),true);
$packageLocal = array_map(function($item) use ($tmpPackageLocal){
    return array_merge($item,$tmpPackageLocal[$item['package_namespace']]);
},$package);

//merge config packageLocal dengan packageLocalEnv nya jika dalam mode dev
if($system['mode']=='dev'){
    $packageLocalEnv = json_decode(file_get_contents(__DIR__.'/../app/MainApp/config/packageLocalEnv.json'),true);
    $packageLocal = array_map(function($item) use ($packageLocalEnv){
        return array_merge($item,$packageLocalEnv[$item['package_namespace']]);
    },$packageLocal);
}
/*
package dan module berisi config yang sama persis
*/
return [
    'client' => $client,
    'system' => $system,
    'packageLocal' => $packageLocal,//config2 dari module dan lib yang sudah diedit per project
    'package' => $package,//config2 default dari module dan lib
    'listener' => $listener
];