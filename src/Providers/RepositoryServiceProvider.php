<?php

namespace Deadlyviruz\Extentions\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $driver = ucfirst(config('extention.driver'));

        if ($driver == 'Custom') {
            $namespace = config('extention.custom_driver');
        } else {
            $namespace = 'Deadlyviruz\Extentions\Repositories\\'.$driver.'Repository';
        }

        $this->app->bind('Deadlyviruz\Extentions\Contracts\Repository', $namespace);
    }
}
