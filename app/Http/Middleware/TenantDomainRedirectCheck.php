<?php

namespace App\Http\Middleware;

use Closure;
// use Illuminate\Support\Facades\Auth;
use App\Facades\Tenant;

/**
 * cek apakah domain tenant harus redirect
 */
class TenantDomainRedirectCheck
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
        if(config('AppConfig.system.multitenant.active') && ($tenant = Tenant::getTenantByDomain())){       
            // jika redirect maka redirect     
            if($tenant['domain']['status']==2) {
                return redirect($tenant['domain']['redirect']);
            }
        }
        return $next($request);
    }
}
