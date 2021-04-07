<?php
/**
 * Load all factory from MainApp and Modules
 */
use Illuminate\Support\Facades\App;

// jika running on tests maka load factories2 yang ada
if(App::runningUnitTests()){
    // load MainApp/database/factories
    $factories = glob(app_path('MainApp/database/factories').DIRECTORY_SEPARATOR.'*');                
    foreach($factories as $factory){
        require($factory);              
    }

    // load MainApp/Modules/*/database/factories
    $langItem = glob(app_path('MainApp/Modules').DIRECTORY_SEPARATOR.'*'.DIRECTORY_SEPARATOR.'database/factories'.DIRECTORY_SEPARATOR.'*');                
    foreach($factories as $factory){
        require($factory);              
    }

    // load vendor/hp-synapse/*/src/database/factories
    $langItem = glob(base_path('vendor/hp-synapse').DIRECTORY_SEPARATOR.'*'.DIRECTORY_SEPARATOR.'src/database/factories'.DIRECTORY_SEPARATOR.'*');                
    foreach($factories as $factory){
        require($factory);              
    }
}