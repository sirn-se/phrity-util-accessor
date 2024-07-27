<?php

namespace Phrity\Util;

/**
 * Accessor utility trait.
 */
trait AccessorTrait
{
    /**
     * Recursive worker function for get() operation.
     * @param mixed $data Data set to access
     * @param string $path Path to access
     * @param mixed $default Default value
     * @return mixed Specified content of data set
     */
    private function accessorGet(mixed $data, array $path, mixed $default): mixed
    {
        if (empty($path)) {
            return $data; // Bottom case
        }
        $current = array_shift($path);
        $data = is_object($data) ? get_object_vars($data) : $data;
        if (is_array($data)) {
            if (array_key_exists($current, $data)) {
                return $this->accessorGet($data[$current], $path, $default);
            }
            return $default; // No match
        }
        return $default; // No match
    }

    /**
     * Recursive worker function for has() operation.
     * @param mixed $data Data set to access
     * @param string $path Path to access
     * @return bool If speciefied content is present
     */
    private function accessorHas(mixed $data, array $path): bool
    {
        if (empty($path)) {
            return true; // Bottom case
        }
        $current = array_shift($path);
        $data = is_object($data) ? get_object_vars($data) : $data;
        if (is_array($data)) {
            if (array_key_exists($current, $data)) {
                return $this->accessorHas($data[$current], $path);
            }
            return false; // No match
        }
        return false; // No match
    }

    private function accessorParsePath(string $path, string $separator): array
    {
        return array_filter(explode($separator, $path), function ($item) {
            return $item !== '';
        });
    }
}
