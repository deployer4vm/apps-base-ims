<?php

namespace App\Http\Middleware;

use Closure;
// use Illuminate\Support\Facades\Auth;
use App\Models\Tenant;

class TenantOnly
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
        if(config('AppConfig.system.multitenant.active') && !config('tenant',false)){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }
        
        return $next($request);
    }
}
