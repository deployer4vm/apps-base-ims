<?php

namespace App\Http\Middleware;

use Closure;
use Facades\hpsynapse\moduser\Services\UserAuth;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use hpsynapse\moduser\Models\ApiToken;


class TokenExpireCheck
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
        if (Auth::check() && ($request->expectsJson() || $request->wantsJson() || $request->ajax())) {
            $lastAccess = (new Carbon(Auth::user()->updated_at))->addMinute(config('session.lifetime'));            
            if($lastAccess->lessThan(now())){
                ApiToken::where('api_token',Auth::user()->api_token)->delete();
                throw new \Illuminate\Auth\AuthenticationException();
                return;
            }else{
                ApiToken::where('api_token',Auth::user()->api_token)->update(['updated_at'=>now()]);
            }
            
        }
        return $next($request);
    }
}
