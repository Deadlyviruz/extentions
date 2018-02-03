<?php

namespace Deadlyviruz\Extentions\Console\Generators;

use Deadlyviruz\Extentions\Console\GeneratorCommand;

class MakeControllerCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extention:module:controller
    	{slug : The slug of the Extention}
    	{name : The name of the controller class}
    	{--resource : Generate a Extention resource controller class}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Extention controller class';

    /**
     * String to store the command type.
     *
     * @var string
     */
    protected $type = 'Extention controller';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('resource')) {
            return __DIR__.'/stubs/controller.resource.stub';
        }

        return __DIR__.'/stubs/controller.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return extention_class($this->argument('slug'), 'Http\\Controllers');
    }
}
