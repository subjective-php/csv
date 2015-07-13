<?php

namespace Chadicus\Csv;

/**
 * Simple class for reading delimited data files
 */
class Reader implements \Iterator
{
    /**
     * The column headers.
     *
     * @var array|null
     */
    private $headers;

    /**
     * The field delimiter (one character only).
     *
     * @var string
     */
    private $delimiter;

    /**
     *  The field enclosure character (one character only).
     *
     * @var string
     */
    private $enclosure;

    /**
     * The escape character (one character only).
     *
     * @var string
     */
    private $escapeChar;

    /**
     * File pointer to the csv file.
     *
     * @var resource
     */
    private $handle;

    /**
     * The current file pointer position.
     *
     * @var integer
     */
    private $position = 0;

    /**
     * The current row within the csv file.
     *
     * @var array|false|null
     */
    private $current = null;

    /**
     * Create a new Reader instance.
     *
     * @param string $file       The full path to the csv file.
     * @param array  $headers    The column headers. If null, the headers will be derived from the first line in the
     *                           file.
     * @param string $delimiter  The field delimiter (one character only).
     * @param string $enclosure  The field enclosure character (one character only).
     * @param string $escapeChar The escape character (one character only).
     */
    public function __construct($file, array $headers = null, $delimiter = ',', $enclosure = '"', $escapeChar = '\\')
    {
        if (!is_string($file)) {
            throw new \InvalidArgumentException('$file must be a string containing a full path to a delimited file');
        }

        if (!is_file($file)) {
            throw new \InvalidArgumentException('$file was not a valid file name');
        }

        if (!is_readable($file)) {
            throw new \InvalidArgumentException('$file was not readable');
        }

        if (!is_string($delimiter) || strlen($delimiter) !== 1) {
            throw new \InvalidArgumentException('$delimiter must be a single character string');
        }

        if (!is_string($enclosure) || strlen($enclosure) !== 1) {
            throw new \InvalidArgumentException('$enclosure must be a single character string');
        }

        if (!is_string($escapeChar) || strlen($escapeChar) !== 1) {
            throw new \InvalidArgumentException('$escapeChar must be a single character string');
        }

        $this->headers = $headers;
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escapeChar = $escapeChar;
        $this->handle = fopen($file, 'r');
    }

    /**
     * Advances to the next row in this csv reader
     *
     * @return mixed
     */
    public function next()
    {
        $raw = fgetcsv($this->handle, 0, $this->delimiter, $this->enclosure, $this->escapeChar);
        if (empty($raw)) {
            $this->current = false;
            return false;
        }

        if ($this->headers === null && $this->current === null) {
            //No headers given, derive from first line of file
            $this->headers = $raw;
            $raw = fgetcsv($this->handle, 0, $this->delimiter, $this->enclosure, $this->escapeChar);
            if (empty($raw)) {
                $this->current = false;
                return false;
            }

            $this->current = array_combine($this->headers, $raw);
            return;
        }

        if ($this->headers !== null && $this->current == null) {
            //Headers given, skip first line if header line
            if ($raw === $this->headers) {
                $raw = fgetcsv($this->handle, 0, $this->delimiter, $this->enclosure, $this->escapeChar);
                if (empty($raw)) {
                    $this->current = false;
                    return false;
                }
            }

            $this->current = array_combine($this->headers, $raw);
            return;
        }

        ++$this->position;

        $this->current = array_combine($this->headers, $raw);
    }

    /**
     * Return the current element.
     *
     * @return array returns array containing values from the current row
     */
    public function current()
    {
        if ($this->current === null) {
            $this->next();
        }

        return $this->current;
    }

    /**
     * Return the key of the current element.
     *
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Rewind the Iterator to the first element.
     *
     * @return void
     */
    public function rewind()
    {
        rewind($this->handle);
        $this->position = 0;
        $this->current = null;
    }

    /**
     * Check if there is a current element after calls to rewind() or next().
     *
     * @return bool true if there is a current element, false otherwise
     */
    public function valid()
    {
        if ($this->current === null) {
            $this->next();
        }

        return !feof($this->handle) && $this->current !== false;
    }

    /**
     * Ensure file handles are closed when all references to this reader are destroyed.
     *
     * @return void
     */
    public function __destruct()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }
}
