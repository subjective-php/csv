<?php

namespace SubjectivePHP\Csv;

use SplFileObject;

interface HeaderStrategyInterface
{
    public function getHeaders(SplFileObject $fileObject) : array;
}
