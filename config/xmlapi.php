<?php
/**
 * config cpanel api
 */
return [    
    'dbcreate_use_cpanel' => env('CPANEL_CREATEDB'),
    'domain' => env('CPANEL_DOMAIN'),    
    'port' => env('CPANEL_PORT',2083),
    'username' => env('CPANEL_USERNAME'),
    'password' => env('CPANEL_PASSWORD')
];