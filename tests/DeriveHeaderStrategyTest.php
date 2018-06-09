<?php

namespace SubjectivePHPTest\Csv;

use SplFileObject;
use SubjectivePHP\Csv\DeriveHeaderStrategy;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \SubjectivePHP\Csv\DeriveHeaderStrategy
 */
final class DeriveHeaderStrategyTest extends TestCase
{
    /**
     * @test
     * @covers ::getHeaders
     */
    public function getHeaders()
    {
        $strategy = new DeriveHeaderStrategy();
        $fileObject = $this->getFileObject();
        $this->assertSame(
            ['id', 'author', 'title', 'genre', 'price', 'publish_date', 'description'],
            $strategy->getHeaders($fileObject)
        );
    }

    /**
     * @test
     * @covers ::isHeaderRow
     */
    public function rowIsHeaderRow()
    {
        $strategy = new DeriveHeaderStrategy();
        $fileObject = $this->getFileObject();
        $strategy->getHeaders($fileObject);
        $this->assertTrue($strategy->isHeaderRow($fileObject->fgetcsv()));
    }

    /**
     * @test
     * @covers ::isHeaderRow
     */
    public function rowNotIsHeaderRow()
    {
        $strategy = new DeriveHeaderStrategy();
        $fileObject = $this->getFileObject();
        $strategy->getHeaders($fileObject);
        $fileObject->fgetcsv();
        $this->assertFalse($strategy->isHeaderRow($fileObject->fgetcsv()));
    }

    private function getFileObject() : SplFileObject
    {
        $fileObject = new SplFileObject(__DIR__ . '/_files/pipe_delimited.txt');
        $fileObject->setFlags(SplFileObject::READ_CSV);
        $fileObject->setCsvControl('|');
        return $fileObject;
    }
}
