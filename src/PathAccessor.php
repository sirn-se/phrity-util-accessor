<?php

namespace Phrity\Util;

/**
 * PathAccessor utility class.
 */
class PathAccessor
{
    use AccessorTrait;

    /**
     * @var string $path Path
     */
    protected $path;

    /**
     * @var string $separator Separator
     */
    protected $separator;

    /**
     * Constructor for this class.
     * @param string $path Path
     * @param string $separator Separator
     */
    public function __construct(string $path, string $separator = '/')
    {
        $this->path = $path;
        $this->separator = $separator;
    }

    /**
     * Get specified content from data set.
     * @param mixed $data Data set to access
     * @param mixed $default Default value
     * @return mixed Specified content of data set
     */
    public function get(mixed $data, $default = null): mixed
    {
        return $this->accessorGet($data, $this->accessorParsePath($this->path, $this->separator), $default);
    }

    /**
     * Check specified content in data set.
     * @param mixed $data Data set to access
     * @return bool If speciefied content is present
     */
    public function has(mixed $data): bool
    {
        return $this->accessorHas($data, $this->accessorParsePath($this->path, $this->separator));
    }

    /**
     * Set specified content on data set.
     * @param mixed $data Data set to modify
     * @param mixed $value Value to set
     * @return mixed Modified data set
     */
    public function set(mixed $data, mixed $value): mixed
    {
        return $this->accessorSet($data, $this->accessorParsePath($this->path, $this->separator), $value);
    }
}
