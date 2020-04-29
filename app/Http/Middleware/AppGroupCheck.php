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
        if(config('AppConfig.system.multitenant.active')){
            if(!($appGroup = $request->header('Group-App'))){
                if(!($appGroup = $request->route('group_app'))){ 
                    $appGroup = $request->input('group_app');
                }
            }    
            if($appGroup && $tenant = Tenant::where('group_app',$appGroup)->first()){                
                resolve('bindTenant',['tenant_id'=>$tenant->id]);
                $config = app('config');
                $config->set('tenant',$tenant->toArray());
            }    
        }
        return $next($request);
    }
}
