<?php

namespace App\Http\Middleware;

use Closure;
// use Illuminate\Support\Facades\Auth;
use Facades\App\Models\Tenant;

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
        $appGroup = $request->header('App-Group');
        if($appGroup){
            $tenant = Tenant::where('group_app',$appGroup)->first();
            if($tenant){
                $config = app('config');
                $config->set('tenant',$tenant->toArray());
            }
        }        
        return $next($request);
    }
}
