<?php

namespace App\Services;

use Illuminate\Support\Facades\App;

class Trans
{   

    public function fallbackLocal()
    {
        return config('AppConfig.system.fallback_locale');
    }

    public function getLocale()
    {
        return App::getLocale();
    }

    public function chose($lang)
    {
        return isset($lang[$this->getLocale()])?$lang[$this->getLocale()]:(isset($lang[$this->fallbackLocal()])?$lang[$this->fallbackLocal()]:$lang);
    }
}