<?php

namespace App\Http\Middleware;

use Closure;
// use Illuminate\Support\Facades\Auth;
use App\Models\Tenant;

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
        if(config('AppConfig.system.web_admin.multitenant.active')){
            if($appGroup = $request->header('App-Group')){
                if(!($appGroup = $request->route('app_group'))){ 
                    $appGroup = $request->input('app_group');
                }
            }    
            if($appGroup && $tenant = Tenant::where('group_app',$appGroup)->first()){                
                $a = resolve('bindTenant',['tenant_id'=>$tenant->id]);
                $config = app('config');
                $config->set('tenant',$tenant->toArray());
            }    
        }
        return $next($request);
    }
}
