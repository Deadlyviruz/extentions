<?php

namespace Deadlyviruz\Extentions\Console\Generators;

use Deadlyviruz\Extentions\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeModelCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:extention:model
    	{slug : The slug of the extention.}
    	{name : The name of the model class.}
        {--migration : Create a new migration file for the model.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new extention model class';

    /**
     * String to store the command type.
     *
     * @var string
     */
    protected $type = 'Extention model';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (parent::handle() !== false) {
            if ($this->option('migration')) {
                $table = Str::plural(Str::snake(class_basename($this->argument('name'))));

                $this->call('make:extention:migration', [
                    'slug'     => $this->argument('slug'),
                    'name'     => "create_{$table}_table",
                    '--create' => $table,
                ]);
            }
        }
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/model.stub';
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
        return extention_class($this->argument('slug'), 'Models');
    }
}
