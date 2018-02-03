<?php

namespace Deadlyviruz\Extentions\Traits;

trait MigrationTrait
{
    /**
     * Require (once) all migration files for the supplied Extention.
     *
     * @param string $extention
     */
    protected function requireMigrations($extention)
    {
        $path = $this->getMigrationPath($extention);

        $migrations = $this->laravel['files']->glob($path.'*_*.php');

        foreach ($migrations as $migration) {
            $this->laravel['files']->requireOnce($migration);
        }
    }

    /**
     * Get migration directory path.
     *
     * @param string $extention
     *
     * @return string
     */
    protected function getMigrationPath($extention)
    {
        return extention_path($extention, 'Database/Migrations');
    }
}
