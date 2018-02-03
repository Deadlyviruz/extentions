<?php

namespace Deadlyviruz\Extentions\Providers;

use Illuminate\Support\ServiceProvider;

class GeneratorServiceProvider extends ServiceProvider
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
        $generators = [
            'command.make.extention'            => \Deadlyviruz\Extentions\Console\Generators\MakeExtentionCommand::class,
            'command.make.extention.controller' => \Deadlyviruz\Extentions\Console\Generators\MakeControllerCommand::class,
            'command.make.extention.middleware' => \Deadlyviruz\Extentions\Console\Generators\MakeMiddlewareCommand::class,
            'command.make.extention.migration'  => \Deadlyviruz\Extentions\Console\Generators\MakeMigrationCommand::class,
            'command.make.extention.model'      => \Deadlyviruz\Extentions\Console\Generators\MakeModelCommand::class,
            'command.make.extention.policy'     => \Deadlyviruz\Extentions\Console\Generators\MakePolicyCommand::class,
            'command.make.extention.provider'   => \Deadlyviruz\Extentions\Console\Generators\MakeProviderCommand::class,
            'command.make.extention.request'    => \Deadlyviruz\Extentions\Console\Generators\MakeRequestCommand::class,
            'command.make.extention.seeder'     => \Deadlyviruz\Extentions\Console\Generators\MakeSeederCommand::class,
            'command.make.extention.test'       => \Deadlyviruz\Extentions\Console\Generators\MakeTestCommand::class,
        ];

        foreach ($generators as $slug => $class) {
            $this->app->singleton($slug, function ($app) use ($slug, $class) {
                return $app[$class];
            });

            $this->commands($slug);
        }
    }
}
