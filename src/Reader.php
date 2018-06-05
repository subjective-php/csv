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
     *
     * @throws \InvalidArgumentException Thrown if $file is not readable.
     * @throws \InvalidArgumentException Thrown if $delimiter is a single character string.
     * @throws \InvalidArgumentException Thrown if $enclosure is a single character string.
     * @throws \InvalidArgumentException Thrown if $escapeChar is a single character string.
     */
    public function __construct(
        string $file,
        array $headers = null,
        string $delimiter = ',',
        string $enclosure = '"',
        string $escapeChar = '\\'
    ) {
        if (!is_readable($file)) {
            throw new \InvalidArgumentException(
                '$file must be a string containing a full path to a readable delimited file'
            );
        }

        if (strlen($delimiter) !== 1) {
            throw new \InvalidArgumentException('$delimiter must be a single character string');
        }

        if (strlen($enclosure) !== 1) {
            throw new \InvalidArgumentException('$enclosure must be a single character string');
        }

        if (strlen($escapeChar) !== 1) {
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
        try {
            $raw = $this->readLine();
            if ($this->current !== null) {
                ++$this->position;
                $this->current = array_combine($this->headers, $raw);
            }

            if ($this->headers === null) {
                //No headers given, derive from first line of file
                $this->headers = $raw;
                $this->current = array_combine($this->headers, $this->readLine());
                return;
            }

            //Headers given, skip first line if header line
            if ($raw === $this->headers) {
                $raw = $this->readLine();
            }

            $this->current = array_combine($this->headers, $raw);
        } catch (\Exception $e) {
            $this->current = false;
            return false;
        }
    }

    /**
     * Helper method to read the next line in the delimited file.
     *
     * @return array|false
     *
     * @throws \Exception Thrown if no data is returned when reading the file.
     */
    private function readLine()
    {
        $raw = fgetcsv($this->handle, 0, $this->delimiter, $this->enclosure, $this->escapeChar);
        if (empty($raw)) {
            throw new \Exception('Empty line read');
        }

        return $raw;
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
     * @return integer
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
