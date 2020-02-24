<?php

use Illuminate\Http\Request;
use App\Services\Utilities;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//jika artisan web access aktif, maka buka
if(config('AppConfig.system.has_artisan_web_access',false)){
    $artisanEndpoind = config('AppConfig.system.has_artisan_web_access','/update/run-artisan/').'{action}';
    Route::get($artisanEndpoind, function(Request $request){
        $command = $request->route('action');
        $return = Utilities::artisan($command);
        return $return;
    });
}