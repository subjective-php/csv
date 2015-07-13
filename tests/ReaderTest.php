<?php
namespace ChadicusTest\Csv;

use Chadicus\Csv\Reader;

/**
 * Unit tests for the Chadicus\Csv\Reader class
 *
 * @coversDefaultClass \Chadicus\Csv\Reader
 * @covers ::__construct
 * @covers ::__destruct
 * @covers ::<private>
 */
final class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Verify basic usage of Reader.
     *
     * @test
     * @covers ::next
     * @covers ::current
     * @covers ::key
     * @covers ::valid
     * @covers ::rewind
     * @dataProvider getReaders()
     *
     * @return void
     */
    public function basicUsage($reader)
    {
        $expected = [
            [
                'id' => 'bk101',
                'author' => 'Gambardella, Matthew',
                'title' => 'XML Developer\'s Guide',
                'genre' => 'Computer',
                'price' => '44.95',
                'publish_date' => '2000-10-01',
                'description' => 'An in-depth look at creating applications with XML.',
            ],
            [
                'id' => 'bk102',
                'author' => 'Ralls, Kim',
                'title' => 'Midnight Rain',
                'genre' => 'Fantasy',
                'price' => '5.95',
                'publish_date' => '2000-12-16',
                'description' => 'A former architect battles corporate zombies and an evil sorceress.',
            ],
            [
                'id' => 'bk103',
                'author' => 'Corets, Eva',
                'title' => 'Maeve Ascendant',
                'genre' => 'Fantasy',
                'price' => '5.95',
                'publish_date' => '2000-11-17',
                'description' => 'Young survivors lay the foundation for a new society in England.',
            ],
        ];

        foreach ($reader as $key => $row) {
            $this->assertSame($expected[$key], $row);
        }
    }

    /**
     * Data provider for basic usage test
     *
     * @return array
     */
    public function getReaders()
    {
        $headers = ['id', 'author', 'title', 'genre', 'price', 'publish_date', 'description'];
        return [
            [new Reader(__DIR__ . '/_files/basic.csv')],
            [new Reader(__DIR__ . '/_files/basic.csv', $headers)],
            [new Reader(__DIR__ . '/_files/no_headers.csv', $headers)],
            [new Reader(__DIR__ . '/_files/pipe_delimited.txt', $headers, '|')],
            [new Reader(__DIR__ . '/_files/tab_delimited.txt', $headers, "\t")],
        ];
    }

    /**
     * Verify parameter checks for $file in __construct().
     *
     * @param mixed The file parameter to check
     *
     * @test
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $file must be a string containing a full path to a readable delimited file
     * @dataProvider getFiles
     *
     * @return void
     */
    public function constructInvalidFileParam($file)
    {
        $reader = new Reader($file);
    }

    /**
     * Data provider for constructInvalidFileParam() test.
     *
     * @return array
     */
    public function getFiles()
    {
        chmod(__DIR__ . '/_files/not_readable.csv', 0220);
        return [
            [__DIR__ . '/_files/not_readable.csv'],
            [true],
            [null],
            [__DIR__ . '/_files/doesnotexist.csv'],
        ];
    }

    /**
     * Verify behavior of __construct with an invalid delimiter.
     *
     * @test
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $delimiter must be a single character string
     *
     * @return void
     */
    public function constructInvalidDelimiter()
    {
        new Reader(__DIR__ . '/_files/basic.csv', null, 'too long');
    }

    /**
     * Verify behavior of __construct with an invalid enclosure.
     *
     * @test
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $enclosure must be a single character string
     *
     * @return void
     */
    public function constructInvalidEnclosure()
    {
        new Reader(__DIR__ . '/_files/basic.csv', null, ',', 123);
    }

    /**
     * Verify behavior of __construct with an invalid escapeChar.
     *
     * @test
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $escapeChar must be a single character string
     *
     * @return void
     */
    public function constructInvalidEscapeChar()
    {
        new Reader(__DIR__ . '/_files/basic.csv', null, ',', '"', null);
    }

    /**
     * Verify behaviour of consecutive rewind().
     *
     * @test
     * @covers ::rewind
     *
     * @return void
     */
    public function consecutiveRewind()
    {
        $reader = new Reader(__DIR__ . '/_files/basic.csv');

        foreach ($reader as $row) {
        }

        $reader->rewind();
        $reader->rewind();
        $this->assertSame(0, $reader->key());
    }

    /**
     * Verify basic behaviour of current().
     *
     * @test
     * @covers ::current
     *
     * @return void
     */
    public function current()
    {
        $reader = new Reader(__DIR__ . '/_files/basic.csv');
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
            $reader->current()
        );
    }

    /**
     * Verify behavior of Reader with an empty file
     *
     * @test
     * @covers ::next
     * @covers ::current
     * @covers ::key
     * @covers ::valid
     * @covers ::rewind
     * @dataProvider getEmptyFiles
     *
     * @return void
     */
    public function emptyFiles($reader)
    {
        $total = 0;

        $reader->rewind();
        while ($reader->valid()) {
            $total++;
            $reader->next();
        }

        $this->assertSame(0, $total);
    }

    /**
     * Data provider for empty file test.
     *
     * @return array
     */
    public function getEmptyFiles()
    {
        $headers = ['id', 'author', 'title', 'genre', 'price', 'publish_date', 'description'];
        return [
            [new Reader(__DIR__ . '/_files/empty.csv')],
            [new Reader(__DIR__ . '/_files/headers_only.csv')],
            [new Reader(__DIR__ . '/_files/headers_only.csv', $headers)],
        ];
    }
}
