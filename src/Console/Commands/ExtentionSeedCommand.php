<?php

namespace Deadlyviruz\Extentions\Console\Commands;

use Deadlyviruz\Extentions\Modules;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ExtentionSeedCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'extention:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the database with records for a specific or all extentions';

    /**
     * @var Extentions
     */
    protected $extention;

    /**
     * Create a new command instance.
     *
     * @param Extention $extention
     */
    public function __construct(Extentions $extention)
    {
        parent::__construct();

        $this->extention = $extention;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $slug = $this->argument('slug');

        if (isset($slug)) {
            if (!$this->extention->exists($slug)) {
                return $this->error('Extention does not exist.');
            }

            if ($this->extention->isEnabled($slug)) {
                $this->seed($slug);
            } elseif ($this->option('force')) {
                $this->seed($slug);
            }

            return;
        } else {
            if ($this->option('force')) {
                $extentions = $this->extention->all();
            } else {
                $extentions = $this->extention->enabled();
            }

            foreach ($extentions as $extention) {
                $this->seed($extention['slug']);
            }
        }
    }

    /**
     * Seed the specific module.
     *
     * @param string $extention
     *
     * @return array
     */
    protected function seed($slug)
    {
        $extention = $this->extention->where('slug', $slug);
        $params = [];
        $namespacePath = $this->module->getNamespace();
        $rootSeeder = $extention['basename'].'DatabaseSeeder';
        $fullPath = $namespacePath.'\\'.$extention['basename'].'\Database\Seeds\\'.$rootSeeder;

        if (class_exists($fullPath)) {
            if ($this->option('class')) {
                $params['--class'] = $this->option('class');
            } else {
                $params['--class'] = $fullPath;
            }

            if ($option = $this->option('database')) {
                $params['--database'] = $option;
            }

            if ($option = $this->option('force')) {
                $params['--force'] = $option;
            }

            $this->call('db:seed', $params);

            event($slug.'.extention.seeded', [$extention, $this->option()]);
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [['slug', InputArgument::OPTIONAL, 'Extention slug.']];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['class', null, InputOption::VALUE_OPTIONAL, 'The class name of the module\'s root seeder.'],
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to seed.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run while in production.'],
        ];
    }
}
