<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DeployInit extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syndeploy:init '
        . '{mode : prod atau dev, dev jika untuk local symlink path, prod jika untuk gitlab reference}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synapse - Environment Mode setter';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $mode = $this->argument('mode')=='dev'?'dev':'prod';        
        
        $config_dev_package_path = config('synapse.dev_package_path');
        
        $composerJson = json_decode(File::get(base_path('composer.json')),true);
        foreach ($composerJson['repositories'] as $key => $value) {
            
            if($mode == 'dev'){
                $package_name = explode('/',$value['name']);
                $repo[] = [
                    "name" => $value['name'],
                    "type" => "path",
                    "url" => $config_dev_package_path.$package_name[1],
                    "options" => [
                        "symlink" => true
                    ]
                ];
            }else{
                $repo[] = [
                    "name" => $value['name'],
                    "type" => "git",
                    "url" => 'https://gitlab.com/'.$value['name'].'.git',
                    "reference" => "master"
                ];
            }
        }
        
        $composerJson['repositories'] = $repo;
        
        if (File::put(base_path('composer.json') , str_replace('\/','/',json_encode($composerJson,JSON_PRETTY_PRINT)) )) {
            $this->info('PROJECT composer.json : updated');
        }
        
        $this->info('Change Development Mode To : '.$mode);
        $this->info('SUCCESS!');
    }
}