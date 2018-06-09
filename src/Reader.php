<?php

namespace SubjectivePHP\Csv;

use SplFileObject;

/**
 * Simple class for reading delimited data files
 */
class Reader implements \Iterator
{
    /**
     * The column headers.
     *
     * @var array
     */
    private $headers;

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
     * @var SplFileObject
     */
    private $fileObject;

    /**
     * @var HeaderStrategyInterface
     */
    private $headerStrategy;

    /**
     * Create a new Reader instance.
     *
     * @param string                  $file           The full path to the csv file.
     * @param HeaderStrategyInterface $headerStrategy Strategy for obtaining headers of the file.
     * @param CsvOptions              $csvOptions     Options for the csv file.
     *
     * @throws \InvalidArgumentException Thrown if $file is not readable.
     */
    public function __construct($file, HeaderStrategyInterface $headerStrategy = null, CsvOptions $csvOptions = null)
    {
        if (!is_readable((string)$file)) {
            throw new \InvalidArgumentException(
                '$file must be a string containing a full path to a readable delimited file'
            );
        }

        $csvOptions = $csvOptions ?? new CsvOptions();
        $this->headerStrategy = $headerStrategy ?? new DeriveHeaderStrategy();

        $this->fileObject = new SplFileObject($file);
        $this->fileObject->setFlags(SplFileObject::READ_CSV);
        $this->fileObject->setCsvControl(
            $csvOptions->getDelimiter(),
            $csvOptions->getEnclosure(),
            $csvOptions->getEscapeChar()
        );

        $this->headers = $this->headerStrategy->getHeaders($this->fileObject);
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

            //Headers given, skip first line if header line
            if ($this->headerStrategy->isHeaderRow($raw)) {
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
        $raw = $this->fileObject->fgetcsv();
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
        $this->fileObject->rewind();
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

        return !$this->fileObject->eof() && $this->current !== false;
    }

    /**
     * Ensure file handles are closed when all references to this reader are destroyed.
     *
     * @return void
     */
    public function __destruct()
    {
        $this->fileObject = null;
    }
}
