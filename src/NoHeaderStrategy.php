<?php

namespace SubjectivePHP\Csv;

use SplFileObject;

/**
 * Header strategy which generates a numeric array whose size is the number of columns in the given file.
 */
final class NoHeaderStrategy implements HeaderStrategyInterface
{
    public function getHeaders(SplFileObject $fileObject) : array
    {
        $firstRow = $fileObject->fgetcsv();
        $headers = array_keys($firstRow);
        $fileObject->rewind();
        return $headers;
    }

    public function isHeaderRow(array $row) : bool
    {
        return false;
    }
}
