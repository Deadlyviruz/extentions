<?php

namespace DummyNamespace\Providers;

use Deadlyviruz\Extentions\Support\ServiceProvider;

class ExtentionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the module services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../Resources/Lang', 'DummySlug');
        $this->loadViewsFrom(__DIR__.'/../Resources/Views', 'DummySlug');
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations', 'DummySlug');
        $this->loadConfigsFrom(__DIR__.'/../config');
    }

    /**
     * Register the module services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }
}
