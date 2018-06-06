<?php

namespace SixtyNine\Cloud\Tests\Builder;

use SixtyNine\Cloud\Factory\FontsFactory;
use SixtyNine\Cloud\FontMetrics;
use PHPUnit\Framework\TestCase;

class FontMetricsTest extends TestCase
{
    public function testMetricsHorizontal()
    {
        $factory = FontsFactory::create(__DIR__ . '/fixtures/fonts');
        $calc = new FontMetrics($factory);

        $size1 = $calc->calculateSize('abc', 'Arial.ttf', 10);
        $size2 = $calc->calculateSize('abc', 'Arial.ttf', 12);
        $size3 = $calc->calculateSize('abc', 'Arial.ttf', 1);
        $size4 = $calc->calculateSize('a', 'Arial.ttf', 1);

        $this->assertTrue($size1->getWidth() < $size2->getWidth());
        $this->assertTrue($size1->getHeight() < $size2->getHeight());
        $this->assertTrue($size3->getWidth() < $size1->getWidth());
        $this->assertTrue($size3->getHeight() < $size1->getHeight());
        $this->assertTrue($size4->getWidth() < $size3->getWidth());
        $this->assertEquals($size4->getHeight(),  $size3->getHeight());
    }

    public function testMetricsVertical()
    {
        $factory = FontsFactory::create(__DIR__ . '/fixtures/fonts');
        $calc = new FontMetrics($factory);

        $size1 = $calc->calculateSize('abc', 'Arial.ttf', 10, 270);
        $size2 = $calc->calculateSize('abc', 'Arial.ttf', 12, 270);
        $size3 = $calc->calculateSize('abc', 'Arial.ttf', 1, 270);
        $size4 = $calc->calculateSize('a', 'Arial.ttf', 1, 270);

        $this->assertTrue($size1->getWidth() < $size2->getWidth());
        $this->assertTrue($size1->getHeight() < $size2->getHeight());
        $this->assertTrue($size3->getWidth() < $size1->getWidth());
        $this->assertTrue($size3->getHeight() < $size1->getHeight());
        $this->assertEquals($size4->getWidth(), $size3->getWidth());
        $this->assertTrue($size4->getHeight() <  $size3->getHeight());
    }

    public function testLoadSingleFont()
    {
        $factory = FontsFactory::create(__DIR__ . '/fixtures/fonts/Arial.ttf', false);
        $calc = new FontMetrics($factory);
        $size1 = $calc->calculateSize('abc', 'Arial.ttf', 10, 270);
        $size2 = $calc->calculateSize('abc', 'Arial.ttf', 12, 270);
        $this->assertTrue($size1->getWidth() < $size2->getWidth());
    }

    public function testLoadMultipleFonts()
    {
        $factory = FontsFactory::create([__DIR__ . '/fixtures/fonts/Arial.ttf'], false);
        $calc = new FontMetrics($factory);
        $size1 = $calc->calculateSize('abc', 'Arial.ttf', 10, 270);
        $size2 = $calc->calculateSize('abc', 'Arial.ttf', 12, 270);
        $this->assertTrue($size1->getWidth() < $size2->getWidth());
    }
}
