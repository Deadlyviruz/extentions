<?php

namespace Deadlyviruz\Extentions\Console\Commands;

use Deadlyviruz\Extentions\Modules;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Arr;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ExtentionMigrateCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'extention:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the database migrations for a specific or all Extentions';

    /**
     * @var Extention
     */
    protected $extention;

    /**
     * @var Migrator
     */
    protected $migrator;

    /**
     * Create a new command instance.
     *
     * @param Migrator $migrator
     * @param Extention  $extention
     */
    public function __construct(Migrator $migrator, Extentions $extention)
    {
        parent::__construct();

        $this->migrator = $migrator;
        $this->extention = $extention;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->prepareDatabase();

        if (!empty($this->argument('slug'))) {
            $extention = $this->module->where('slug', $this->argument('slug'));

            if ($this->extention->isEnabled($extention['slug'])) {
                return $this->migrate($extention['slug']);
            } elseif ($this->option('force')) {
                return $this->migrate($extention['slug']);
            } else {
                return $this->error('Nothing to migrate.');
            }
        } else {
            if ($this->option('force')) {
                $extentions = $this->extention->all();
            } else {
                $extentions = $this->extention->enabled();
            }

            foreach ($extentions as $extention) {
                $this->migrate($extention['slug']);
            }
        }
    }

    /**
     * Run migrations for the specified module.
     *
     * @param string $slug
     *
     * @return mixed
     */
    protected function migrate($slug)
    {
        if ($this->extention->exists($slug)) {
            $extention = $this->extention->where('slug', $slug);
            $pretend = Arr::get($this->option(), 'pretend', false);
            $step = Arr::get($this->option(), 'step', false);
            $path = $this->getMigrationPath($slug);

            $this->migrator->run($path, ['pretend' => $pretend, 'step' => $step]);

            event($slug.'.extention.migrated', [$extention, $this->option()]);

            // Once the migrator has run we will grab the note output and send it out to
            // the console screen, since the migrator itself functions without having
            // any instances of the OutputInterface contract passed into the class.
            foreach ($this->migrator->getNotes() as $note) {
                if (!$this->option('quiet')) {
                    $this->line($note);
                }
            }

            // Finally, if the "seed" option has been given, we will re-run the database
            // seed task to re-populate the database, which is convenient when adding
            // a migration and a seed at the same time, as it is only this command.
            if ($this->option('seed')) {
                $this->call('extention:seed', ['extention' => $slug, '--force' => true]);
            }
        } else {
            return $this->error('Extention does not exist.');
        }
    }

    /**
     * Get migration directory path.
     *
     * @param string $slug
     *
     * @return string
     */
    protected function getMigrationPath($slug)
    {
        return extention_path($slug, 'Database/Migrations');
    }

    /**
     * Prepare the migration database for running.
     */
    protected function prepareDatabase()
    {
        $this->migrator->setConnection($this->option('database'));

        if (!$this->migrator->repositoryExists()) {
            $options = ['--database' => $this->option('database')];

            $this->call('migrate:install', $options);
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
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run while in production.'],
            ['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'],
            ['seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run.'],
            ['step', null, InputOption::VALUE_NONE, 'Force the migrations to be run so they can be rolled back individually.'],
        ];
    }
}
