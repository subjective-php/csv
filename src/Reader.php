<?php

namespace SubjectivePHP\Csv;

use SplFileObject;

/**
 * Simple class for reading delimited data files
 */
class Reader implements \IteratorAggregate
{
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
    public function __construct(string $file, HeaderStrategyInterface $headerStrategy = null, CsvOptions $csvOptions = null)
    {
        $this->fileObject = $this->getFileObject($file, $csvOptions ?? new CsvOptions());
        $this->headerStrategy = $headerStrategy ?? new DeriveHeaderStrategy();
        $this->headerStrategy->getHeaders($this->fileObject);
    }

    public function getIterator() : \Traversable
    {
        return $this->getOuterIterator(
            $this->getInnerIterator()
        );
    }

    private function getFileObject(string $filePath, CsvOptions $csvOptions) : SplFileObject
    {
        if (!is_readable($filePath)) {
            throw new \InvalidArgumentException(
                '$file must be a string containing a full path to a readable delimited file'
            );
        }

        $fileObject = new SplFileObject($filePath);
        $fileObject->setFlags(SplFileObject::READ_CSV | SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY);
        $fileObject->setCsvControl(
            $csvOptions->getDelimiter(),
            $csvOptions->getEnclosure(),
            $csvOptions->getEscapeChar()
        );

        return $fileObject;
    }

    public function __destruct()
    {
        $this->fileObject = null;
    }

    private function getInnerIterator() : \CallbackFilterIterator
    {
        $strategy = $this->headerStrategy;
        return new \CallbackFilterIterator(
            $this->fileObject,
            function ($current) use ($strategy) {
                return !$strategy->isHeaderRow($current);
            }
        );
    }

    private function getOuterIterator(\CallbackFilterIterator $innerIterator) : \IteratorIterator
    {
        return new class($innerIterator, $this->headerStrategy) extends \IteratorIterator
        {
            private $strategy;

            public function __construct(\Traversable $innerIterator, HeaderStrategyInterface $strategy)
            {
                parent::__construct($innerIterator);
                $this->strategy = $strategy;
            }

            public function current()
            {
                return $this->strategy->createDataRow(parent::current());
            }
        };
    }
}
