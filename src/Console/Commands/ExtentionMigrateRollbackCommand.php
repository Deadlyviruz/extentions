<?php

namespace Deadlyviruz\Extentions\Console\Commands;

use Deadlyviruz\Extentions\Modules;
use Deadlyviruz\Extentions\Traits\MigrationTrait;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Database\Migrations\Migrator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ExtentionMigrateRollbackCommand extends Command
{
    use MigrationTrait, ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'extention:migrate:rollback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback the last database migrations for a specific or all Extentions';

    /**
     * The migrator instance.
     *
     * @var \Illuminate\Database\Migrations\Migrator
     */
    protected $migrator;

    /**
     * @var Extentions
     */
    protected $extention;

    /**
     * Create a new command instance.
     *
     * @param Migrator $migrator
     * @param Extentions  $extention
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
        if (!$this->confirmToProceed()) {
            return;
        }

        $this->migrator->setConnection($this->option('database'));

        $paths = $this->getMigrationPaths();
        $this->migrator->rollback(
            $paths, ['pretend' => $this->option('pretend'), 'step' => (int) $this->option('step')]
        );

        foreach ($this->migrator->getNotes() as $note) {
            $this->output->writeln($note);
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
            ['step', null, InputOption::VALUE_OPTIONAL, 'The number of migrations to be reverted.'],
        ];
    }

    /**
     * Get all of the migration paths.
     *
     * @return array
     */
    protected function getMigrationPaths()
    {
        $slug = $this->argument('slug');
        $paths = [];

        if ($slug) {
            $paths[] = extention_path($slug, 'Database/Migrations');
        } else {
            foreach ($this->extention->all() as $extention) {
                $paths[] = extention_path($extention['slug'], 'Database/Migrations');
            }
        }

        return $paths;
    }
}
