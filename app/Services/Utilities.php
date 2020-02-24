<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
class Utilities
{
    public static function findNamespaceResources(array $namespaces, $resourceFolderName, $resourceNamespace)
    {        
        return array_reduce($namespaces, function ($carry, $namespacePath) use ($resourceNamespace, $resourceFolderName) {
            $modulePrefix = $namespacePath[1];
            $components = glob(sprintf('%s*', $namespacePath[0]), GLOB_ONLYDIR);           
            $isModuleOk = true;            
            $paths = array_map(function ($component) use ($resourceNamespace, $resourceFolderName, $modulePrefix, $isModuleOk) {
                
                if($modulePrefix){
                    $moduleName = substr($component, strrpos($component, DIRECTORY_SEPARATOR) + 1);       
                    
                    if(!is_array($modulePrefix))$modulePrefix = [$modulePrefix];

                    foreach ($modulePrefix as $val) {
                        if(strpos($moduleName, $val) !== 0)    
                            return false;
                    }   
                    
                    $component .= DIRECTORY_SEPARATOR.'src';
                }
                
                $path = [$component];

                if (!empty($resourceNamespace)) {
                    $path[] = $resourceNamespace;
                }
                
                $path[] = $resourceFolderName;

                $path = implode(DIRECTORY_SEPARATOR, $path);

                return is_dir($path) ? $path : false;
            }, $components);

            return array_merge($carry, array_filter($paths));
        }, []);
    }
    
    /*
     * looping per module per module namespace nya (letak modul bisa dimana saja)
     */
    public static function listModulePath(array $namespaces,$func)
    {
        $return = [];

        //$path[0] = path ke namespace
        //$path[1] = prefix directory yang ada di path bersangkutan, false jika tanpa prefix
        foreach ($namespaces as $namespace => $path) {
            $tmp = glob(sprintf('%s*', $path[0]),GLOB_ONLYDIR);
            foreach ($tmp as $modulePath){                
                //$component : nama/folder module nya
                $component = substr($modulePath, strrpos($modulePath, DIRECTORY_SEPARATOR) + 1);
                
                //cek jika ada prefix maka hanya ambil path yg sesuai prefix nya saja
                if($path[1]){
                    if(!is_array($path[1]))$path[1] = [$path[1]];
                    $skipModule = false;
                    foreach ($path[1] as $val) {
                        if(strpos($component, $val) !== 0)    
                            $skipModule = true;
                    }
                    if($skipModule)    
                        continue;
                }
                
                if($namespace=='hpsynapse'){
                    $modulePath .= DIRECTORY_SEPARATOR.'src';
                }
                
                //$newNamespace : namespace ke folder per modulenya App/Modules/NAMAMODULE
                $newNamespace = sprintf(
                    '%s\\%s\\',
                    $namespace,
                    str_replace('-', '', $component)
                );

                $return[] = $func($newNamespace,$modulePath);
                
            }
        }
        return $return;
    }

    /**
     * eksekusi perintah artisan
     * 
     * @param string $command perintah artisan, misal 'config:cache'
     */
    public static function artisan($command ='')
    {
        //list perintah artisan yg hanya bisa dieksekusi langsung via command line, tidak bisa via class Artisan
        $shellOnlyCommands = [
            'clear-compiled',
            'package:discover',
            'backup:run',
            'passport:client --password',
            'passport:install',
            'apidoc:generate',
            'route:list',
            'config:cache',
            'config:clear',
            'migrate',
            'db:seed',
            'route:cache',
            'route:clear',
            'view:cache',
            'view:clear',
            'optimize:clear',
            'optimize'
        ];
        $ret = '';
        if(in_array($command,$shellOnlyCommands)){
            $ret = shell_exec('cd '.base_path('').' && php artisan ' . $command);
        }else{
            Artisan::call($command);
            $ret = Artisan::output();
        }
        return ['comamnd'=>$command,'return'=>$ret];
    }
}
