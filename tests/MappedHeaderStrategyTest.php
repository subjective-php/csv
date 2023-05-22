<?php

namespace SubjectivePHPTest\Csv;

use SplFileObject;
use SubjectivePHP\Csv\MappedHeaderStrategy;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \SubjectivePHP\Csv\MappedHeaderStrategy
 * @covers ::__construct
 */
final class MappedHeaderStrategyTest extends TestCase
{
    const HEADER_MAP = [
        'id' => 'Book ID',
        'author' => 'Author',
        'title' => 'Title',
        'genre' => 'Genre',
        'price' => 'Price',
        'publish_date' => 'Publish Date',
        'description' => 'Description',
    ];

    /**
     * @test
     * @covers ::getHeaders
     */
    public function getHeaders()
    {
        $fileObject = $this->getFileObject();
        $strategy = $this->getStrategy();
        $this->assertSame(array_values(self::HEADER_MAP), $strategy->getHeaders($fileObject));
    }

    /**
     * @test
     * @covers ::isHeaderRow
     */
    public function rowIsHeaderRow()
    {
        $strategy = $this->getStrategy();
        $this->assertTrue($strategy->isHeaderRow(array_keys(self::HEADER_MAP)));
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
                'Book ID' => 'bk101',
                'Author' => 'Gambardella, Matthew',
                'Title' => 'XML Developer\'s Guide',
                'Genre' => 'Computer',
                'Price' => '44.95',
                'Publish Date' => '2000-10-01',
                'Description' => 'An in-depth look at creating applications with XML.',
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

    private function getStrategy() : MappedHeaderStrategy
    {
        return new MappedHeaderStrategy(self::HEADER_MAP);
    }
}
