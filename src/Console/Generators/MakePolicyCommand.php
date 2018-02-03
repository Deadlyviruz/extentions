<?php

namespace Deadlyviruz\Extentions\Console\Generators;

use Deadlyviruz\Extentions\Console\GeneratorCommand;

class MakePolicyCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:extention:policy
    	{slug : The slug of the extention.}
    	{name : The name of the policy class.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new extention policy class';

    /**
     * String to store the command type.
     *
     * @var string
     */
    protected $type = 'Extention policy';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/policy.stub';
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
        return extention_class($this->argument('slug'), 'Policies');
    }
}
