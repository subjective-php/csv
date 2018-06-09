<?php
namespace SubjectivePHPTest\Csv;

use SubjectivePHP\Csv\CsvOptions;
use SubjectivePHP\Csv\DeriveHeaderStrategy;
use SubjectivePHP\Csv\MappedHeaderStrategy;
use SubjectivePHP\Csv\NoHeaderStrategy;
use SubjectivePHP\Csv\ProvidedHeaderStrategy;
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
    private $unreadableFilePath;

    public function setUp()
    {
        $this->unreadableFilePath = tempnam(sys_get_temp_dir(), 'csv');
        touch($this->unreadableFilePath);
        chmod($this->unreadableFilePath, 0220);
    }

    public function tearDown()
    {
        unlink($this->unreadableFilePath);
    }

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
     * @test
     * @covers ::next
     * @covers ::current
     * @covers ::key
     * @covers ::valid
     * @covers ::rewind
     */
    public function readWithCustomHeaders()
    {
        $expected = [
            [
                'Book ID' => 'bk101',
                'Author' => 'Gambardella, Matthew',
                'Title' => 'XML Developer\'s Guide',
                'Genre' => 'Computer',
                'Price' => '44.95',
                'Publish Date' => '2000-10-01',
                'Description' => 'An in-depth look at creating applications with XML.',
            ],
            [
                'Book ID' => 'bk102',
                'Author' => 'Ralls, Kim',
                'Title' => 'Midnight Rain',
                'Genre' => 'Fantasy',
                'Price' => '5.95',
                'Publish Date' => '2000-12-16',
                'Description' => 'A former architect battles corporate zombies and an evil sorceress.',
            ],
            [
                'Book ID' => 'bk103',
                'Author' => 'Corets, Eva',
                'Title' => 'Maeve Ascendant',
                'Genre' => 'Fantasy',
                'Price' => '5.95',
                'Publish Date' => '2000-11-17',
                'Description' => 'Young survivors lay the foundation for a new society in England.',
            ],
        ];

        $strategy = new MappedHeaderStrategy(
            [
                'id' => 'Book ID',
                'author' => 'Author',
                'title' => 'Title',
                'genre' => 'Genre',
                'price' => 'Price',
                'publish_date' => 'Publish Date',
                'description' => 'Description',
            ]
        );

        $reader = new Reader(__DIR__ . '/_files/basic.csv', $strategy);
        foreach ($reader as $key => $row) {
            $this->assertSame($expected[$key], $row);
        }
    }

    /**
     * @test
     */
    public function readNoHeaders()
    {
        $expected = [
            [
                'bk101',
                'Gambardella, Matthew',
                'XML Developer\'s Guide',
                'Computer',
                '44.95',
                '2000-10-01',
                'An in-depth look at creating applications with XML.',
            ],
            [
                'bk102',
                'Ralls, Kim',
                'Midnight Rain',
                'Fantasy',
                '5.95',
                '2000-12-16',
                'A former architect battles corporate zombies and an evil sorceress.',
            ],
            [
                'bk103',
                'Corets, Eva',
                'Maeve Ascendant',
                'Fantasy',
                '5.95',
                '2000-11-17',
                'Young survivors lay the foundation for a new society in England.',
            ],
        ];

        $reader = new Reader(__DIR__ . '/_files/no_headers.csv', new NoHeaderStrategy());
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
            [new Reader(__DIR__ . '/_files/basic.csv', new ProvidedHeaderStrategy($headers))],
            [new Reader(__DIR__ . '/_files/no_headers.csv', new ProvidedHeaderStrategy($headers))],
            [
                new Reader(
                    __DIR__ . '/_files/pipe_delimited.txt',
                    new ProvidedHeaderStrategy($headers),
                    new CsvOptions('|')
                )
            ],
            [
                new Reader(
                    __DIR__ . '/_files/tab_delimited.txt',
                    new ProvidedHeaderStrategy($headers),
                    new CsvOptions("\t")
                )
            ],
        ];
    }

    /**
     * Verify parameter checks for $file in __construct().
     *
     * @param mixed $file The file parameter to check.
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
        return [
            [__DIR__ . '/_files/not_readable.csv'],
            [true],
            [null],
            [__DIR__ . '/_files/doesnotexist.csv'],
        ];
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
    public function getEmptyFiles()
    {
        $headers = ['id', 'author', 'title', 'genre', 'price', 'publish_date', 'description'];
        return [
            [new Reader(__DIR__ . '/_files/empty.csv')],
            [new Reader(__DIR__ . '/_files/headers_only.csv')],
            [new Reader(__DIR__ . '/_files/headers_only.csv', new ProvidedHeaderStrategy($headers))],
        ];
    }
}
