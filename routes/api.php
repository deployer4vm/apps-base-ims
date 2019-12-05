<?php

// use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

/**
 * Config
 * ------------------------------------------
 */
//db config
$group = [
    'prefix' => config('AppConfig.system.config_endpoint'),
    'middleware' => 'auth:api'
];
Route::group($group,function(){  
    Route::get('/', 'ConfigController@readList');
    //create atau update config
    Route::post('/', 'ConfigController@createUpdate');
});

//access config
Route::get('/getconfig/access', 'ConfigController@accessConfig');


/**
 * tenant api related
 * ------------------------------------------
 */
//db config
$group = [
    'prefix' => 'sys/tenant',
    // 'middleware' => 'auth:api'
];
Route::group($group,function(){  
    //list tenant
    Route::get('/','TenantController@listTenant');
});