<?php

namespace Deadlyviruz\Extentions\Console\Commands;

use Illuminate\Console\Command;

class ExtentionOptimizeCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'extention:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize the Extention cache for better performance';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Generating optimized module cache');

        $this->laravel['extentions']->optimize();

        event('extentions.optimized', [$this->laravel['extentions']->all()]);
    }
}
