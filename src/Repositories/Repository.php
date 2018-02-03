<?php

namespace Deadlyviruz\Extentions\Repositories;

use Exception;
use Deadlyviruz\Extentions\Contracts\Repository as RepositoryContract;
use Illuminate\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem;

abstract class Repository implements RepositoryContract
{
    /**
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @var string Path to the defined modules directory
     */
    protected $path;

    /**
     * Constructor method.
     *
     * @param \Illuminate\Config\Repository     $config
     * @param \Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(Config $config, Filesystem $files)
    {
        $this->config = $config;
        $this->files = $files;
    }

    /**
     * Get all module basenames.
     *
     * @return array
     */
    protected function getAllBasenames()
    {
        $path = $this->getPath();

        try {
            $collection = collect($this->files->directories($path));

            $basenames = $collection->map(function ($item, $key) {
                return basename($item);
            });

            return $basenames;
        } catch (\InvalidArgumentException $e) {
            return collect([]);
        }
    }

    /**
     * Get a Extention's manifest contents.
     *
     * @param string $slug
     *
     * @return Collection|null
     */
    public function getManifest($slug)
    {
        if (! is_null($slug)) {
            $path     = $this->getManifestPath($slug);
            $contents = $this->files->get($path);
            $validate = @json_decode($contents, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $collection = collect(json_decode($contents, true));

                return $collection;
            }

            throw new Exception('['.$slug.'] Your JSON manifest file was not properly formatted. Check for formatting issues and try again.');
        }
    }

    /**
     * Get Extentions path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path ?: $this->config->get('extentions.path');
    }

    /**
     * Set Extentions path in "RunTime" mode.
     *
     * @param string $path
     *
     * @return object $this
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path for the specified Extention.
     *
     * @param string $slug
     *
     * @return string
     */
    public function getModulePath($slug)
    {
        $extention = studly_case(str_slug($slug));

        if (\File::exists($this->getPath()."/{extention}/")) {
            return $this->getPath()."/{$extention}/";
        }

        return $this->getPath()."/{$slug}/";
    }

    /**
     * Get path of Extention manifest file.
     *
     * @param $slug
     *
     * @return string
     */
    protected function getManifestPath($slug)
    {
        return $this->getModulePath($slug).'extention.json';
    }

    /**
     * Get Extentions namespace.
     *
     * @return string
     */
    public function getNamespace()
    {
        return rtrim($this->config->get('extentions.namespace'), '/\\');
    }
}
