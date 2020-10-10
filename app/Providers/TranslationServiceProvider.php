<?php

namespace App\Providers;

use Illuminate\Translation\TranslationServiceProvider as BaseTranslationServiceProvider;
use App\Services\Translation\DistributedFileLoader;

class TranslationServiceProvider extends BaseTranslationServiceProvider
{

    /**
     * Register the translation line loader.
     *
     * @return void
     */
    protected function registerLoader()
    {
        $this->app->singleton('translation.loader', function ($app) {
            return new DistributedFileLoader($app['files'], config('hpsynapse.lang_path',[]));
        });
    }
}
