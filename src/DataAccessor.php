<?php

namespace Phrity\Util;

/**
 * DataAccessor utility class.
 */
class DataAccessor
{
    use AccessorTrait;

    /**
     * @var string $separator Separator
     */
    protected $separator;

    /**
     * @var mixed $data Data set
     */
    protected $data;

    /**
     * Constructor for this class.
     * @param mixed $data Data set to access
     * @param string $separator Separator
     */
    public function __construct($data, string $separator = '/')
    {
        $this->data = $data;
        $this->separator = $separator;
    }

    /**
     * Get specified content from data set.
     * @param string $path Path to access
     * @param mixed $default Default value
     * @return mixed Specified content of data set
     */
    public function get(string $path, $default = null)
    {
        return $this->accessorGet($this->data, $this->accessorParsePath($path, $this->separator), $default);
    }

    /**
     * Check specified content in data set.
     * @param string $path Path to access
     * @return bool If speciefied content is present
     */
    public function has(string $path): bool
    {
        return $this->accessorHas($this->data, $this->accessorParsePath($path, $this->separator));
    }
}
