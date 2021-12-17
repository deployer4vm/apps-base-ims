<?php

namespace App\Http\Middleware\DELETED;

use Closure;
// use Illuminate\Support\Facades\Auth;
use App\Facades\Tenant;

/**
 * SUDAH TIDAK DIGUNAKAN
 * dipindah ke routeserviceprovider
 */
class AppGroupCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(config('AppConfig.system.multitenant.active')){
            if(!($appGroup = $request->header('Group-App'))){
                if(!($appGroup = $request->route('group_app'))){ 
                    $appGroup = $request->input('group_app');
                }
            }    
            if($appGroup)
                Tenant::setActiveTenantByGroup($appGroup);  
        }
        return $next($request);
    }
}
