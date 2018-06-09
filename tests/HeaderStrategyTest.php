<?php

namespace SubjectivePHPTest\Csv;

use SplFileObject;
use SubjectivePHP\Csv\HeaderStrategy;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \SubjectivePHP\Csv\HeaderStrategy
 * @covers ::getHeaders
 * @covers ::<private>
 */
final class HeaderStrategyTest extends TestCase
{
    /**
     * @test
     * @covers ::derive
     */
    public function derive()
    {
        $fileObject = $this->getFileObject('pipe_delimited.txt', '|');
        $strategy = HeaderStrategy::derive();
        $this->assertSame(
            ['id', 'author', 'title', 'genre', 'price', 'publish_date', 'description'],
            $strategy->getHeaders($fileObject)
        );
    }

    /**
     * @test
     * @covers ::provide
     */
    public function provide()
    {
        $headers = ['id', 'author', 'title', 'genre', 'price', 'publish_date', 'description'];
        $fileObject = $this->getFileObject('basic.csv');
        $strategy = HeaderStrategy::provide($headers);
        $this->assertSame($headers, $strategy->getHeaders($fileObject));
    }

    /**
     * @test
     * @covers ::none
     */
    public function none()
    {
        $fileObject = $this->getFileObject('no_headers.csv');
        $strategy = HeaderStrategy::none();
        $this->assertSame(
            [0, 1, 2, 3, 4, 5, 6],
            $strategy->getHeaders($fileObject)
        );
    }

    public function getFileObject(string $fileName, string $delimiter = ',') : SplFileObject
    {
         $fileObject = new SplFileObject(__DIR__ . "/_files/{$fileName}");
         $fileObject->setFlags(SplFileObject::READ_CSV);
         $fileObject->setCsvControl($delimiter);
         return $fileObject;
    }
}
