<?php

namespace SubjectivePHP\Csv;

use SplFileObject;

/**
 * Header strategy which uses the provided headers array.
 */
final class ProvidedHeaderStrategy implements HeaderStrategyInterface
{
    /**
     * @var array
     */
    private $headers;

    public function __construct(array $headers)
    {
        $this->headers = $headers;
    }

    public function getHeaders(SplFileObject $fileObject) : array
    {
        return $this->headers;
    }

    public function isHeaderRow(array $row) : bool
    {
        return $row === $this->headers;
    }

    public function createDataRow(array $row) : array
    {
        return array_combine($this->headers, $row);
    }
}
