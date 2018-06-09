<?php

namespace SubjectivePHPTest\Csv;

use SplFileObject;
use SubjectivePHP\Csv\ProvidedHeaderStrategy;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \SubjectivePHP\Csv\ProvidedHeaderStrategy
 * @covers ::__construct
 */
final class ProvidedHeaderStrategyTest extends TestCase
{
    const HEADERS = ['id', 'author', 'title', 'genre', 'price', 'publish_date', 'description'];

    /**
     * @test
     * @covers ::getHeaders
     */
    public function getHeaders()
    {
        $fileObject = $this->getFileObject();
        $strategy = $this->getStrategy();
        $this->assertSame(self::HEADERS, $strategy->getHeaders($fileObject));
    }

    /**
     * @test
     * @covers ::isHeaderRow
     */
    public function rowIsHeaderRow()
    {
        $strategy = $this->getStrategy();
        $this->assertTrue($strategy->isHeaderRow(self::HEADERS));
    }

    /**
     * @test
     * @covers ::isHeaderRow
     */
    public function rowIsNotHeaderRow()
    {
        $strategy = $this->getStrategy();
        $fileObject = $this->getFileObject();
        $fileObject->fgetcsv();
        $this->assertFalse($strategy->isHeaderRow($fileObject->fgetcsv()));
    }

    private function getFileObject() : SplFileObject
    {
        $fileObject = new SplFileObject(__DIR__ . '/_files/basic.csv');
        $fileObject->setFlags(SplFileObject::READ_CSV);
        $fileObject->setCsvControl(',');
        return $fileObject;
    }

    private function getStrategy() : ProvidedHeaderStrategy
    {
        return new ProvidedHeaderStrategy(self::HEADERS);
    }
}
