<?php

namespace SubjectivePHP\Csv;

final class CsvOptions
{
    /**
     * @var string
     */
    private $delimiter;

    /**
     * @var string
     */
    private $enclosure;

    /**
     * @var string
     */
    private $escapeChar;

    /**
     * Construct a new CsvOptions instance.
     *
     * @param string $delimiter The field delimiter (one character only).
     * @param string $enclosure The field enclosure character (one character only).
     * @param string $escapeChar The escape character (one character only).
     */
    public function __construct(string $delimiter = ',', string $enclosure = '"', string $escapeChar = '\\')
    {
        if (strlen($delimiter) !== 1) {
            throw new \InvalidArgumentException('$delimiter must be a single character string');
        }

        if (strlen($enclosure) !== 1) {
            throw new \InvalidArgumentException('$enclosure must be a single character string');
        }

        if (strlen($escapeChar) !== 1) {
            throw new \InvalidArgumentException('$escapeChar must be a single character string');
        }

        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escapeChar = $escapeChar;
    }

    /**
     * Gets the field delimiter (one character only).
     *
     * @return string
     */
    public function getDelimiter() : string
    {
        return $this->delimiter;
    }

    /**
     * Gets the field enclosure character (one character only).
     *
     * @return string
     */
    public function getEnclosure() : string
    {
        return $this->enclosure;
    }

    /**
     * Gets the escape character (one character only).
     *
     * @return string
     */
    public function getEscapeChar() : string
    {
        return $this->escapeChar;
    }
}
