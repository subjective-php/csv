<?php

namespace SubjectivePHP\Csv;

use SplFileObject;

/**
 * Header strategy that uses a map array to provide custom headers for a csv file.
 */
final class MappedHeaderStrategy implements HeaderStrategyInterface
{
    /**
     * @var array
     */
    private $headerMap;

    public function __construct(array $headerMap)
    {
        $this->headerMap = $headerMap;
    }

    public function getHeaders(SplFileObject $fileObject) : array
    {
        return array_values($this->headerMap);
    }

    public function isHeaderRow(array $row) : bool
    {
        $headers = array_keys($this->headerMap);
        sort($row);
        sort($headers);
        return $row === $headers;
    }

    public function createDataRow(array $row) : array
    {
        $result = [];
        $originalHeaders = array_keys($this->headerMap);
        foreach ($originalHeaders as $index => $key) {
            $newHeader = $this->headerMap[$key];
            $result[$newHeader] = $row[$index] ?? null;
        }

        return $result;
    }
}
