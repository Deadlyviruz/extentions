<?php

namespace Deadlyviruz\Extentions\Console;

use Illuminate\Console\GeneratorCommand as LaravelGeneratorCommand;
use Illuminate\Support\Str;
use Extention;

abstract class GeneratorCommand extends LaravelGeneratorCommand
{
    /**
     * Parse the name and format according to the root namespace.
     *
     * @param string $name
     *
     * @return string
     */
    protected function qualifyClass($name)
    {
        $rootNamespace = config('extentions.namespace');

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        $name = str_replace('/', '\\', $name);

        return $this->qualifyClass(
            $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name
        );
    }

    /**
     * Get the destination class path.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getPath($name)
    {
        $slug = $this->argument('slug');

        // take everything after the extention name in the given path (ignoring case)
        $key = array_search(strtolower($slug), explode('\\', strtolower($name)));
        if ($key === false) {
            $newPath = str_replace('\\', '/', $name);
        } else {
            $newPath = implode('/', array_slice(explode('\\', $name), $key + 1));
        }

        return extention_path($slug, "$newPath.php");
    }
}
