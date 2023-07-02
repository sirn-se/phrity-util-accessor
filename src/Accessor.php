<?php

namespace Phrity\Util;

/**
 * Accessor utility class.
 */
class Accessor
{
    /**
     * @var string $separator Separator
     */
    private $separator;

    /**
     * Constructor for this class.
     * @param string $separator Separator
     */
    public function __construct(string $separator = '/')
    {
        $this->separator = $separator;
    }

    /**
     * Get specified content from data set.
     * @param mixed $data Data set to access
     * @param string $path Path to access
     * @param mixed $default Default value
     * @return mixed Specified content of data set
     */
    public function get($data, string $path, $default = null)
    {
        return $this->accessorGet($data, array_filter(explode($this->separator, $path), function ($item) {
            return $item !== '';
        }), $default);
    }

    /**
     * Check specified content in data set.
     * @param mixed $data Data set to access
     * @param string $path Path to access
     * @return bool If speciefied content is present
     */
    public function has($data, string $path): bool
    {
        return $this->accessorHas($data, array_filter(explode($this->separator, $path), function ($item) {
            return $item !== '';
        }));
    }

    private function accessorGet($data, array $path, $default)
    {
        if (empty($path)) {
            return $data; // Bottom case
        }
        $current = array_shift($path);
        if (is_array($data)) {
            if (array_key_exists($current, $data)) {
                return $this->accessorGet($data[$current], $path, $default);
            }
            return $default; // No match
        }
        if (is_object($data)) {
            if (property_exists($data, $current)) {
                return $this->accessorGet($data->$current, $path, $default);
            }
            return $default; // No match
        }
        return $default; // No match
    }

    private function accessorHas($data, array $path): bool
    {
        if (empty($path)) {
            return true; // Bottom case
        }
        $current = array_shift($path);
        if (is_array($data)) {
            if (array_key_exists($current, $data)) {
                return $this->accessorHas($data[$current], $path);
            }
            return false; // No match
        }
        if (is_object($data)) {
            if (property_exists($data, $current)) {
                return $this->accessorHas($data->$current, $path);
            }
            return false; // No match
        }
        return false; // No match
    }
}
