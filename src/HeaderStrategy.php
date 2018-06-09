<?php

namespace SubjectivePHP\Csv;

use SplFileObject;

final class HeaderStrategy implements HeaderStrategyInterface
{
    /**
     * @var callable
     */
    private $getHeadersCallable;

    private function __construct(callable $getHeadersCallable)
    {
        $this->getHeadersCallable = $getHeadersCallable;
    }

    /**
     * Create header strategy which derives the headers from the first line of the file.
     *
     * @return HeaderstrategyInterface
     */
    public static function derive() : HeaderStrategyInterface
    {
        return new DeriveHeaderStrategy();
    }

    /**
     * Create header strategy which uses the provided headers array.
     *
     * @return HeaderstrategyInterface
     */
    public static function provide(array $headers) : HeaderStrategyInterface
    {
        return new self(
            function () use ($headers) : array {
                return $headers;
            }
        );
    }

    /**
     * Create header strategy which generates a numeric array whose size is the number of columns in the given file.
     *
     * @return HeaderstrategyInterface
     */
    public static function none() : HeaderStrategyInterface
    {
        return new NoHeaderStrategy();
    }

    /**
     * Extracts headers from the given SplFileObject.
     *
     * @param SplFileObject $fileObject The delimited file containing the headers.
     *
     * @return array
     */
    public function getHeaders(SplFileObject $fileObject) : array
    {
        return ($this->getHeadersCallable)($fileObject);
    }
}
