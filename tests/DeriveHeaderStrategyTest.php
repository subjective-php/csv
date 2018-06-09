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
        $fileObject = new SplFileObject(__DIR__ . '/_files/pipe_delimited.txt');
        $fileObject->setFlags(SplFileObject::READ_CSV);
        $fileObject->setCsvControl('|');
        $strategy = new DeriveHeaderStrategy();
        $this->assertSame(
            ['id', 'author', 'title', 'genre', 'price', 'publish_date', 'description'],
            $strategy->getHeaders($fileObject)
        );
    }
}
