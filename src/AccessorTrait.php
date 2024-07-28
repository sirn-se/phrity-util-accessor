<?php

namespace Phrity\Util;

use stdClass;

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

    /**
     * Recursive worker function for set() operation.
     * @param mixed $data Data set to modify
     * @param string $path Path to modify
     * @param mixed $value Value to set
     * @return mixed Modified data set
     */
    private function accessorSet(mixed $data, array $path, mixed $value): mixed
    {
        if (empty($path)) {
            return $value; // Bottom case
        }
        $current = array_shift($path);
        if (is_object($data)) {
            if (!$data instanceof stdClass && !array_key_exists($current, get_object_vars($data))) {
                throw new AccessorException("Can not set property '{$current}' on " . get_debug_type($data));
            }
            $data = clone $data;
            $data->$current = $this->accessorSet($data->$current ?? null, $path, $value);
        }
        if (is_array($data)) {
            $data[$current] = $this->accessorSet($data[$current] ?? null, $path, $value);
        }
        if (is_null($data) || is_scalar($data)) {
            $data = [];
            $data[$current] = $this->accessorSet(null, $path, $value);
        }
        return $data;
    }

    /**
     * parse string path into array segments.
     * @param string $path Path to parse
     * @param string $separator Separator token
     * @return array Path segments as array
     */
    private function accessorParsePath(string $path, string $separator): array
    {
        return array_filter(explode($separator, $path), function (string $item): bool {
            return $item !== '';
        });
    }
}
