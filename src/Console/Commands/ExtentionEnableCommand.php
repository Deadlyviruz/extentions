<?php

namespace Deadlyviruz\Extentions\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class ExtentionEnableCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'extention:enable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable an Extention';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $slug = $this->argument('slug');

        if ($this->laravel['extentions']->isDisabled($slug)) {
            $this->laravel['extentions']->enable($slug);

            $extention = $this->laravel['extentions']->where('slug', $slug);

            event($slug.'.extention.enabled', [$extention, null]);

            $this->info('Extention was enabled successfully.');
        } else {
            $this->comment('Extention is already enabled.');
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
