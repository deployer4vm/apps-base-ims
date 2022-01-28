<?php

use Illuminate\Support\Facades\Route;
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
Route::group(['prefix'=>'system-queue'],function(){
    Route::group(['prefix'=>'export'],function(){
        Route::get('list', 'QueueController@listQueue')->name('system.queue.export.list');
        Route::get('list/history', 'QueueController@historyQueue')->name('system.queue.export.history');
        Route::get('detail/{cacheKey}', 'QueueController@detailQueue')->name('system.queue.export.detail');
        Route::get('detail/{cacheKey}/cancel', 'QueueController@cancelQueue')->name('system.queue.export.cancel');
        Route::get('detail/{cacheKey}/delete', 'QueueController@deleteQueue')->name('system.queue.export.delete');
    });
    Route::group(['prefix'=>'import'],function(){
        Route::get('list', 'ImportQueueController@listQueue')->name('system.queue.import.list');
        Route::get('list/history', 'ImportQueueController@historyQueue')->name('system.queue.import.history');
        Route::get('detail/{cacheKey}', 'ImportQueueController@detailQueue')->name('system.queue.import.detail');
        Route::get('detail/{cacheKey}/cancel', 'ImportQueueController@cancelQueue')->name('system.queue.import.cancel');
        Route::get('detail/{cacheKey}/delete', 'ImportQueueController@deleteQueue')->name('system.queue.import.delete');
    });
});

Route::get('/storage{any}', 'StorageController@index')->where('any', '.*');
//jika artisan web access aktif, maka buka
if(config('AppConfig.system.has_artisan_web_access',false)){
    $artisanEndpoind = config('AppConfig.system.has_artisan_web_access','/update/run-artisan/').'{action}';
    Route::get($artisanEndpoind, function(Request $request){
        $command = $request->route('action');
        $return = Utilities::artisan($command);
        return $return;
    });
}