<?php

use Illuminate\Database\Seeder;
use App\Services\Utilities;

use App\Models\Seed;
// use Exception;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $modulePath = Utilities::listModulePath(config('hpsynapse.namespaces'), function($namespace,$pathToModule){              
            $moduleNamespace = explode('\\',trim($namespace,'\\'));
            $moduleNamespace = array_pop($moduleNamespace);     
            $pathToModule .= DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'SeedList.php';
            if(config('AppConfig.packageLocal.'.$moduleNamespace.'.database.run_seed',true) && file_exists($pathToModule)){
                return $pathToModule;
            }
        });
        
        $projectSeeds = include(app_path('MainApp/database/SeedList.php'));
        $moduleSeeds = [];
        foreach($modulePath as $value){
            if($value) $moduleSeeds = array_merge($moduleSeeds,include($value));
        }
        $projectSeeds = array_merge($moduleSeeds,$projectSeeds);

        foreach($projectSeeds as $class){
            if(!Seed::where('seed',$class)->exists()){
                try {
                    $this->call($class);
                    Seed::create(['seed'=>$class]);
                } catch (Exception $th) {
                    throw $th;
                }
                
            }
        }

    }
}
