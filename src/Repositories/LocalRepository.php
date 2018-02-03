<?php

namespace Deadlyviruz\Extentions\Repositories;

class LocalRepository extends Repository
{
    /**
     * Get all Extentions.
     *
     * @return Collection
     */
    public function all()
    {
        return $this->getCache()->sortBy('order');
    }

    /**
     * Get all Extention slugs.
     *
     * @return Collection
     */
    public function slugs()
    {
        $slugs = collect();

        $this->all()->each(function ($item, $key) use ($slugs) {
            $slugs->push(strtolower($item['slug']));
        });

        return $slugs;
    }

    /**
     * Get modules based on where clause.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return Collection
     */
    public function where($key, $value)
    {
        return collect($this->all()->where($key, $value)->first());
    }

    /**
     * Sort modules by given key in ascending order.
     *
     * @param string $key
     *
     * @return Collection
     */
    public function sortBy($key)
    {
        $collection = $this->all();

        return $collection->sortBy($key);
    }

    /**
     * Sort modules by given key in ascending order.
     *
     * @param string $key
     *
     * @return Collection
     */
    public function sortByDesc($key)
    {
        $collection = $this->all();

        return $collection->sortByDesc($key);
    }

    /**
     * Determines if the given Extention exists.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function exists($slug)
    {
        return $this->slugs()->contains($slug);
    }

    /**
     * Returns count of all Extentions.
     *
     * @return int
     */
    public function count()
    {
        return $this->all()->count();
    }

    /**
     * Get a module property value.
     *
     * @param string $property
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($property, $default = null)
    {
        list($slug, $key) = explode('::', $property);

        $extention = $this->where('slug', $slug);

        return $extention->get($key, $default);
    }

    /**
     * Set the given module property value.
     *
     * @param string $property
     * @param mixed  $value
     *
     * @return bool
     */
    public function set($property, $value)
    {
        list($slug, $key) = explode('::', $property);

        $cachePath = $this->getCachePath();
        $cache = $this->getCache();
        $extention = $this->where('slug', $slug);

        if (isset($extention[$key])) {
            unset($extention[$key]);
        }

        $extention[$key] = $value;

        $extention = collect([$extention['basename'] => $extention]);

        $merged = $cache->merge($extention);
        $content = json_encode($merged->all(), JSON_PRETTY_PRINT);

        return $this->files->put($cachePath, $content);
    }

    /**
     * Get all enabled Extentions.
     *
     * @return Collection
     */
    public function enabled()
    {
        return $this->all()->where('enabled', true);
    }

    /**
     * Get all disabled Extentions.
     *
     * @return Collection
     */
    public function disabled()
    {
        return $this->all()->where('enabled', false);
    }

    /**
     * Check if specified Extention is enabled.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function isEnabled($slug)
    {
        $extention = $this->where('slug', $slug);

        return $extention['enabled'] === true;
    }

    /**
     * Check if specified Extemtopm is disabled.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function isDisabled($slug)
    {
        $extention = $this->where('slug', $slug);

        return $extention['enabled'] === false;
    }

    /**
     * Enables the specified Extention.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function enable($slug)
    {
        return $this->set($slug.'::enabled', true);
    }

    /**
     * Disables the specified module.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function disable($slug)
    {
        return $this->set($slug.'::enabled', false);
    }

    /*
    |--------------------------------------------------------------------------
    | Optimization Methods
    |--------------------------------------------------------------------------
    |
    */

    /**
     * Update cached repository of module information.
     *
     * @return bool
     */
    public function optimize()
    {
        $cachePath = $this->getCachePath();

        $cache = $this->getCache();
        $basenames = $this->getAllBasenames();
        $extentions = collect();

        $basenames->each(function ($extention, $key) use ($extentions, $cache) {
            $basename = collect(['basename' => $extention]);
            $temp = $basename->merge(collect($cache->get($extention)));
            $manifest = $temp->merge(collect($this->getManifest($extention)));

            $extentions->put($extention, $manifest);
        });

        $extentions->each(function ($extention) {
            $extention->put('id', crc32($extention->get('slug')));

            if (!$extention->has('enabled')) {
                $extention->put('enabled', config('extention.enabled', true));
            }

            if (!$extention->has('order')) {
                $extention->put('order', 9001);
            }

            return $extention;
        });

        $content = json_encode($extentions->all(), JSON_PRETTY_PRINT);

        return $this->files->put($cachePath, $content);
    }

    /**
     * Get the contents of the cache file.
     *
     * @return Collection
     */
    private function getCache()
    {
        $cachePath = $this->getCachePath();

        if (!$this->files->exists($cachePath)) {
            $this->createCache();

            $this->optimize();
        }

        return collect(json_decode($this->files->get($cachePath), true));
    }

    /**
     * Create an empty instance of the cache file.
     *
     * @return Collection
     */
    private function createCache()
    {
        $cachePath = $this->getCachePath();
        $content = json_encode([], JSON_PRETTY_PRINT);

        $this->files->put($cachePath, $content);

        return collect(json_decode($content, true));
    }

    /**
     * Get the path to the cache file.
     *
     * @return string
     */
    private function getCachePath()
    {
        return storage_path('app/extention.json');
    }
}
