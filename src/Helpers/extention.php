<?php

use Deadlyviruz\Extentions\Exceptions\ExtentionNotFoundException;

if (!function_exists('extention_path')) {
    /**
     * Return the path to the given module file.
     *
     * @param string $slug
     * @param string $file
     *
     * @return string
     * @throws \Deadlyviruz\Extentions\Exceptions\ExtentionNotFoundException
     */
    function extention_path($slug = null, $file = '')
    {
        $extentionsPath = config('extentions.path');
        $pathMap = config('extentions.pathMap');

        if (!empty($file) && !empty($pathMap)) {
            $file = str_replace(
                array_keys($pathMap),
                array_values($pathMap),
                $file
            );
        }

        $filePath = $file ? '/'.ltrim($file, '/') : '';

        if (is_null($slug)) {
            if (empty($file)) {
                return $extentionsPath;
            }

            return $extentionsPath.$filePath;
        }

        $extention = Extention::where('slug', $slug);

        if ( is_null($extention) ) {
            throw new ExtentionNotFoundException($slug);
        }

        return $extentionsPath.'/'.$extention['basename'].$filePath;
    }
}

if (!function_exists('extention_class')) {
    /**
     * Return the full path to the given module class.
     *
     * @param string $slug
     * @param string $class
     *
     * @return string
     * @throws \Deadlyviruz\Extentions\Exceptions\ExtentionNotFoundException
     */
    function extention_class($slug, $class)
    {
        $extention = Extention::where('slug', $slug);

        if ( is_null($extention) ) {
            throw new ExtentionNotFoundException($slug);
        }

        $namespace = config('extentions.namespace').$extention['basename'];

        return "{$namespace}\\{$class}";
    }
}
