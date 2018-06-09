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

    /**
     * @test
     * @covers ::createDataRow
     */
    public function createDataRow()
    {
        $row = [
            'bk101',
            'Gambardella, Matthew',
            'XML Developer\'s Guide',
            'Computer',
            '44.95',
            '2000-10-01',
            'An in-depth look at creating applications with XML.',
        ];
        $strategy = $this->getStrategy();
        $this->assertSame(
            [
                'id' => 'bk101',
                'author' => 'Gambardella, Matthew',
                'title' => 'XML Developer\'s Guide',
                'genre' => 'Computer',
                'price' => '44.95',
                'publish_date' => '2000-10-01',
                'description' => 'An in-depth look at creating applications with XML.',
            ],
            $strategy->createDataRow($row)
        );
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
