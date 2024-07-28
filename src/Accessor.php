<?php

namespace Phrity\Util;

/**
 * Accessor utility class.
 */
class Accessor
{
    use AccessorTrait;

    /**
     * @var string $separator Separator
     */
    protected $separator;

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
    public function get(mixed $data, string $path, mixed $default = null): mixed
    {
        return $this->accessorGet($data, $this->accessorParsePath($path, $this->separator), $default);
    }

    /**
     * Check specified content in data set.
     * @param mixed $data Data set to access
     * @param string $path Path to access
     * @return bool If speciefied content is present
     */
    public function has(mixed $data, string $path): bool
    {
        return $this->accessorHas($data, $this->accessorParsePath($path, $this->separator));
    }

    /**
     * Set specified content on data set.
     * @param mixed $data Data set to modify
     * @param string $path Path to access
     * @param mixed $value Value to set
     * @return mixed Modified data set
     */
    public function set(mixed $data, string $path, mixed $value): mixed
    {
        return $this->accessorSet($data, $this->accessorParsePath($path, $this->separator), $value);
    }
}
