<?php

namespace SubjectivePHPTest\Csv;

use SplFileObject;
use SubjectivePHP\Csv\NoHeaderStrategy;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \SubjectivePHP\Csv\NoHeaderStrategy
 */
final class NoHeaderStrategyTest extends TestCase
{
    /**
     * @test
     * @covers ::getHeaders
     */
    public function getHeaders()
    {
        $fileObject = new SplFileObject(__DIR__ . '/_files/no_headers.csv');
        $fileObject->setFlags(SplFileObject::READ_CSV);
        $fileObject->setCsvControl(',');
        $strategy = new NoHeaderStrategy();
        $this->assertSame(
            [0, 1, 2, 3, 4, 5, 6],
            $strategy->getHeaders($fileObject)
        );
    }

    /**
     * @test
     * @covers ::isHeaderRow
     */
    public function isHeaderRowAlwaysReturnsFalse()
    {
        $fileObject = new SplFileObject(__DIR__ . '/_files/no_headers.csv');
        $fileObject->setFlags(SplFileObject::READ_CSV);
        $fileObject->setCsvControl(',');
        $strategy = new NoHeaderStrategy();
        $this->assertFalse($strategy->isHeaderRow($fileObject->fgetcsv()));
        $this->assertFalse($strategy->isHeaderRow($fileObject->fgetcsv()));
    }
}
