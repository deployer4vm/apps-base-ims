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
 * -------------------------------------------------
 */

$group = [
    'prefix' => config('AppConfig.system.config_endpoint'),
];

Route::group($group,function(){
    //db config
    Route::group([
            'middleware' => 'auth:api'
        ],function(){  
        Route::get('/', 'ConfigController@readList');
        //create atau update config
        Route::post('/', 'ConfigController@createUpdate');
    });

    //access config
    Route::group(['prefix' => 'access'],function(){
        Route::get('/', 'ConfigController@accessConfig');
        Route::get('/unlock', 'ConfigController@unlockAccess');
        Route::middleware('auth:api')->put('/', 'ConfigController@unlockAccess');
    });

});

/**
 * Tenant
 * -------------------------------------------------
 */

$group = [
    'prefix' => 'sys/tenant',
    // 'middleware' => 'auth:api'
];
Route::group($group,function(){  
    // /api/sys/tenant/active
    Route::get(config('AppConfig.system.web_admin.multitenant.api_endpoint.tenant_active'),'TenantController@activeTenant');    
    // /api/sys/tenant/group
    Route::get(config('AppConfig.system.web_admin.multitenant.api_endpoint.tenant_group'),'TenantController@tenantGroupList');
    //----read tenant resource
    //list tenant - /api/sys/tenant
    Route::get('/','TenantController@listTenant');
});

/**
 * languange
 * -------------------------------------------------
 */
// /api/sys/lang
Route::get(config('AppConfig.system.lang_endpoint'),'LangController@readList');
Route::get('sys/lang','LangController@readList');
