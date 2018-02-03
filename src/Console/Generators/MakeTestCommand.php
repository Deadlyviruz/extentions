<?php

namespace Deadlyviruz\Extentions\Console\Generators;

use Deadlyviruz\Extentions\Console\GeneratorCommand;

class MakeTestCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:extention:test
    	{slug : The slug of the extention}
    	{name : The name of the test class}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new extention test class';

    /**
     * String to store the command type.
     *
     * @var string
     */
    protected $type = 'Extention test';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/test.stub';
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
        return extention_class($this->argument('slug'), 'Tests');
    }
}
