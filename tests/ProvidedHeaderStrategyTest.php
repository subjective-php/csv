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
    /**
     * @test
     * @covers ::getHeaders
     */
    public function getHeaders()
    {
        $headers = ['id', 'author', 'title', 'genre', 'price', 'publish_date', 'description'];
        $fileObject = new SplFileObject(__DIR__ . '/_files/basic.csv');
        $fileObject->setFlags(SplFileObject::READ_CSV);
        $fileObject->setCsvControl(',');
        $strategy = new ProvidedHeaderStrategy($headers);
        $this->assertSame($headers, $strategy->getHeaders($fileObject));
    }
}
