<?php

namespace Deadlyviruz\Extentions;


use Deadlyviruz\Extentions\Contracts\Repository;
use Deadlyviruz\Extentions\Providers\BladeServiceProvider;
use Deadlyviruz\Extentions\Providers\ConsoleServiceProvider;
use Deadlyviruz\Extentions\Providers\GeneratorServiceProvider;
use Deadlyviruz\Extentions\Providers\HelperServiceProvider;
use Deadlyviruz\Extentions\Providers\RepositoryServiceProvider;


use Illuminate\Support\ServiceProvider;


class Extentions extends ServiceProvider
{
    protected $defer = false;

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/extentions.php' => config_path('extentions.php'),
        ], 'config');

        $this->app['extentions']->register();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/extentions.php', 'extentions'
        );

        $this->app->register(ConsoleServiceProvider::class);
        $this->app->register(GeneratorServiceProvider::class);
        $this->app->register(HelperServiceProvider::class);
        $this->app->register(RepositoryServiceProvider::class);
        $this->app->register(BladeServiceProvider::class);

        $this->app->singleton('extentions', function ($app) {
            $repository = $app->make(Repository::class);

            return new Extentions($app, $repository);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['extentions'];
    }

    public static function compiles()
    {
        $extentions = app()->make('extentions')->all();
        $files   = [];

        foreach ($extentions as $extention) {
            $serviceProvider = extention_class($extention['slug'], 'Providers\\ExtentionsServiceProvider');

            if (class_exists($serviceProvider)) {
                $files = array_merge($files, forward_static_call([$serviceProvider, 'compiles']));
            }
        }

        return array_map('realpath', $files);
    }
}
