<?php

namespace Deadlyviruz\Extentions\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class ExtentionDisableCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'extention:disable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable a Extention';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $slug = $this->argument('slug');

        if ($this->laravel['extentions']->isEnabled($slug)) {
            $this->laravel['extentions']->disable($slug);

            $extention = $this->laravel['extentions']->where('slug', $slug);

            event($slug.'.extention.disabled', [$extention, null]);

            $this->info('Extention was disabled successfully.');
        } else {
            $this->comment('Extention is already disabled.');
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['slug', InputArgument::REQUIRED, 'Extention slug.'],
        ];
    }
}
