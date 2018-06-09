<?php

namespace SubjectivePHP\Csv;

use SplFileObject;

/**
 * Header strategy which derives the headers from the first line of the file.
 */
final class DeriveHeaderStrategy implements HeaderStrategyInterface
{
    /**
     * @var array
     */
    private $headers;

    /**
     * Extracts headers from the given SplFileObject.
     *
     * @param SplFileObject $fileObject The delimited file containing the headers.
     *
     * @return array
     */
    public function getHeaders(SplFileObject $fileObject) : array
    {
        $this->headers = $fileObject->fgetcsv();
        $fileObject->rewind();
        return $this->headers;
    }

    public function isHeaderRow(array $row) : bool
    {
        return $row === $this->headers;
    }
}
