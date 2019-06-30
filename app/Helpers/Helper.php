<?php
if (!function_exists('route_api_opt')) {
    /**
     * Autogenerate option route untuk api menyesuakan url home dan api nya
     * 
     * @param type $appsUrl
     * @param type $apiUrl
     * @return array format 
     *      prefix
     *      domain
     */
    function route_api_opt($appsUrl,$apiUrl){
        $appsPathInfo = parse_url($appsUrl);
        $apiPathInfo = parse_url($apiUrl);
        if(!isset($appsPathInfo['path']))$appsPathInfo['path']='';
        if(!isset($apiPathInfo['path']))$apiPathInfo['path']='';
        $prefix = str_replace($appsPathInfo['path'],'',$apiPathInfo['path']);
        if($prefix){  
            $routeOpt['prefix'] = $prefix;  
        }
        if($appsPathInfo['host'] != $apiPathInfo['host']){
            $routeOpt['domain'] = $apiPathInfo['host']; 
        }
        return $routeOpt;
    }
}
if (!function_exists('route_web_opt')) {
    /**
     * Autogenerate option route untuk api menyesuakan url home dan api nya
     * 
     * @param type $appsUrl
     * @param type $apiUrl
     * @return array format 
     *      prefix
     *      domain
     */
    function route_web_opt($appsUrl,$apiUrl){
        $appsPathInfo = parse_url($appsUrl);
        $apiPathInfo = parse_url($apiUrl);
        if(!isset($appsPathInfo['path']))$appsPathInfo['path']='';
        if(!isset($apiPathInfo['path']))$apiPathInfo['path']='';
        $routeOpt = [];
        if($appsPathInfo['host'] != $apiPathInfo['host']){
            $routeOpt['domain'] = $appsPathInfo['host']; 
        }
        return $routeOpt;
    }
}