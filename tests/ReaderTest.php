<?php
namespace SubjectivePHPTest\Csv;

use SubjectivePHP\Csv\Reader;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the SubjectivePHP\Csv\Reader class
 *
 * @coversDefaultClass \SubjectivePHP\Csv\Reader
 * @covers ::__construct
 * @covers ::__destruct
 * @covers ::<private>
 */
final class ReaderTest extends TestCase
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
     * @param Reader $reader The Reader instance to use in the test.
     *
     * @return void
     */
    public function basicUsage(Reader $reader)
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
    public static function getReaders()
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
     * @param mixed $file The file parameter to check.
     *
     * @test
     * @covers ::__construct
     * @dataProvider getFiles
     *
     * @return void
     */
    public function constructInvalidFileParam($file)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$file must be a string containing a full path to a readable delimited file');
        $reader = new Reader($file);
    }

    /**
     * Data provider for constructInvalidFileParam() test.
     *
     * @return array
     */
    public static function getFiles()
    {
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
     *
     * @return void
     */
    public function constructInvalidDelimiter()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$delimiter must be a single character string');
        new Reader(__DIR__ . '/_files/basic.csv', null, 'too long');
    }

    /**
     * Verify behavior of __construct with an invalid enclosure.
     *
     * @test
     * @covers ::__construct
     *
     * @return void
     */
    public function constructInvalidEnclosure()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$enclosure must be a single character string');
        new Reader(__DIR__ . '/_files/basic.csv', null, ',', 123);
    }

    /**
     * Verify behavior of __construct with an invalid escapeChar.
     *
     * @test
     * @covers ::__construct
     *
     * @return void
     */
    public function constructInvalidEscapeChar()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$escapeChar must be a single character string');
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
        $count = 0;
        foreach ($reader as $row) {
            $count++;
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
     * @param Reader $reader The reader instance to use in the tests.
     *
     * @return void
     */
    public function emptyFiles(Reader $reader)
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
    public static function getEmptyFiles()
    {
        $headers = ['id', 'author', 'title', 'genre', 'price', 'publish_date', 'description'];
        return [
            [new Reader(__DIR__ . '/_files/empty.csv')],
            [new Reader(__DIR__ . '/_files/headers_only.csv')],
            [new Reader(__DIR__ . '/_files/headers_only.csv', $headers)],
        ];
    }
}
