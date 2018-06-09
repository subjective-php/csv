<?php

namespace SubjectivePHP\Csv;

use SplFileObject;

interface HeaderStrategyInterface
{
    public function getHeaders(SplFileObject $fileObject) : array;

    public function isHeaderRow(array $row) : bool;
}
