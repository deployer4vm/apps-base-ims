<?php

namespace App\Services;

use App\Facades\Trans;

class Web
{   

    /**
     * BREAD CRUMB
     * -------------------------------------------------------------------------
     */

    private 
        $breadcrumbs = [],
        $breadcrumbTitle = '';

    /**
     * 
     * @param String $text
     * @param String|Array $route
     */
    public function addBreadcrumb($text, $route = '#')
    {
        $this->breadcrumbs[] = [
            'text' => $text,
            'route' => is_array($route)?route($route[0],isset($route[1])?$route[1]:[]):$route
        ];
    }

    public function getBreadcrumb()
    {
        return $this->breadcrumbs;
    }
    
    public function resetBreadcrumb($setHome=true,$homeRoute='dashboard')
    {
        $this->breadcrumbs = [];
        if($setHome)
            $this->addBreadcrumb(__('lang.home'),[$homeRoute]);
    }

    public function setBreadcrumbTitle($title)
    {
        return $this->breadcrumbTitle = Trans::chose($title);
    }

    public function appendBreadcrumbTitle($title)
    {
        return $this->breadcrumbTitle =  $this->breadcrumbTitle.' \ '.Trans::chose($title);
    }

    public function getBreadcrumbTitle()
    {
        return $this->breadcrumbTitle;
    }


}