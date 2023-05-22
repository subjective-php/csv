<?php

namespace SubjectivePHPTest\Csv;

use SubjectivePHP\Csv\CsvOptions;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \SubjectivePHP\Csv\CsvOptions
 * @covers ::__construct
 */
final class CsvOptionsTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $delimiter must be a single character string
     */
    public function constructWithDelimiterGreaterThanOneCharacter()
    {
        new CsvOptions('too long');
    }

    /**
     * @test
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $enclosure must be a single character string
     */
    public function constructWithEnclosureGreaterThanOneCharacter()
    {
        new CsvOptions(',', '##');
    }

    /**
     * @test
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $escapeChar must be a single character string
     */
    public function constructWithEscapeCharGreaterThanOneCharacter()
    {
        new CsvOptions(',', '"', '\\\\');
    }

    /**
     * @test
     * @covers ::getDelimiter
     */
    public function getDelimiter()
    {
        $this->assertSame(',', (new CsvOptions(',', '"', '\\'))->getDelimiter());
    }

    /**
     * @test
     * @covers ::getEnclosure
     */
    public function getEnclosure()
    {
        $this->assertSame('"', (new CsvOptions(',', '"', '\\'))->getEnclosure());
    }

    /**
     * @test
     * @covers ::getEscapeChar
     */
    public function getEscapeChar()
    {
        $this->assertSame('\\', (new CsvOptions(',', '"', '\\'))->getEscapeChar());
    }
}
