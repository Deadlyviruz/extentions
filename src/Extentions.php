<?php

namespace Deadlyviruz\Extentions;

use Deadlyviruz\Extentions\Contracts\Repository;
use Deadlyviruz\Extentions\Exceptions\ExtentionNotFoundException;

use Illuminate\Foundation\Application;

class Extentions
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * Create a new Extention instance.
     *
     * @param Application $app
     * @param Repository  $repository
     */
    public function __construct(Application $app, Repository $repository)
    {
        $this->app = $app;
        $this->repository = $repository;
    }

    /**
     * Register the extention service provider file from all modules.
     *
     * @return void
     */
    public function register()
    {
        $extentions = $this->repository->enabled();

        $extentions->each(function ($extention) {
            try {
                $this->registerServiceProvider($extention);

                $this->autoloadFiles($extention);
            } catch (ExtentionNotFoundException $e) {
                //
            }
        });
    }

    /**
     * Register the module service provider.
     *
     * @param array $extention
     *
     * @return void
     */
    private function registerServiceProvider($extention)
    {
        $serviceProvider = extention_class($extention['slug'], 'Providers\\ExtentionsServiceProvider');

        if (class_exists($serviceProvider)) {
            $this->app->register($serviceProvider);
        }
    }

    /**
     * Autoload custom module files.
     *
     * @param array $extention
     *
     * @return void
     */
    private function autoloadFiles($extention)
    {
        if (isset($extention['autoload'])) {
            foreach ($extention['autoload'] as $file) {
                $path = extention_path($extention['slug'], $file);

                if (file_exists($path)) {
                    include $path;
                }
            }
        }
    }

    /**
     * Oh sweet sweet magical method.
     *
     * @param string $method
     * @param mixed  $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->repository, $method], $arguments);
    }
}
