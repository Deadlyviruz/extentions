<?php

namespace Deadlyviruz\Extentions\Providers;


use Deadlyviruz\Extentions\Database\Migrations\Migrator;
use Illuminate\Support\ServiceProvider;

class ConsoleServiceProvider extends ServiceProvider
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
        $this->registerDisableCommand();
        $this->registerEnableCommand();
        $this->registerListCommand();
        $this->registerMigrateCommand();
        $this->registerMigrateRefreshCommand();
        $this->registerMigrateResetCommand();
        $this->registerMigrateRollbackCommand();
        $this->registerOptimizeCommand();
        $this->registerSeedCommand();
    }

    protected function registerDisableCommand()
    {
        $this->app->singleton('command.extention.disable', function () {
            return new \Deadlyviruz\Extentions\Console\Commands\ExtentionDisableCommand();
        });

        $this->commands('command.extention.disable');
    }

    /**
     * Register the module:enable command.
     */
    protected function registerEnableCommand()
    {
        $this->app->singleton('command.extention.enable', function () {
            return new \Deadlyviruz\Extentions\Console\Commands\ExtentionEnableCommand();
        });

        $this->commands('command.extention.enable');
    }

    /**
     * Register the module:list command.
     */
    protected function registerListCommand()
    {
        $this->app->singleton('command.extention.list', function ($app) {
            return new \Deadlyviruz\Extentions\Console\Commands\ExtentionListCommand($app['extentions']);
        });

        $this->commands('command.extention.list');
    }

    /**
     * Register the module:migrate command.
     */
    protected function registerMigrateCommand()
    {
        $this->app->singleton('command.extention.migrate', function ($app) {
            return new \Deadlyviruz\Extentions\Console\Commands\ExtentionMigrateCommand($app['migrator'], $app['extentions']);
        });

        $this->commands('command.extention.migrate');
    }

    /**
     * Register the module:migrate:refresh command.
     */
    protected function registerMigrateRefreshCommand()
    {
        $this->app->singleton('command.extention.migrate.refresh', function () {
            return new \Deadlyviruz\Extentions\Console\Commands\ExtentionMigrateRefreshCommand();
        });

        $this->commands('command.extention.migrate.refresh');
    }

    /**
     * Register the module:migrate:reset command.
     */
    protected function registerMigrateResetCommand()
    {
        $this->app->singleton('command.extention.migrate.reset', function ($app) {
            return new \Deadlyviruz\Extentions\Console\Commands\ExtentionMigrateResetCommand($app['extentions'], $app['files'], $app['migrator']);
        });

        $this->commands('command.extention.migrate.reset');
    }

    /**
     * Register the module:migrate:rollback command.
     */
    protected function registerMigrateRollbackCommand()
    {
        $this->app->singleton('command.extention.migrate.rollback', function ($app) {
            $repository = $app['migration.repository'];
            $table = $app['config']['database.migrations'];

            $migrator = new Migrator($table, $repository, $app['db'], $app['files']);

            return new \Deadlyviruz\Extentions\Console\Commands\ExtentionMigrateRollbackCommand($migrator, $app['extentions']);
        });

        $this->commands('command.extention.migrate.rollback');
    }

    /**
     * Register the module:optimize command.
     */
    protected function registerOptimizeCommand()
    {
        $this->app->singleton('command.extention.optimize', function () {
            return new \Deadlyviruz\Extentions\Console\Commands\ExtentionOptimizeCommand();
        });

        $this->commands('command.extention.optimize');
    }

    /**
     * Register the module:seed command.
     */
    protected function registerSeedCommand()
    {
        $this->app->singleton('command.extention.seed', function ($app) {
            return new \Deadlyviruz\Extentions\Console\Commands\ExtentionSeedCommand($app['extentions']);
        });

        $this->commands('command.extention.seed');
    }




}
