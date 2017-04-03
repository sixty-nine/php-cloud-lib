<?php

namespace SixtyNine\Cloud\Tests\Renderer;

use SixtyNine\Cloud\Factory\FontsFactory;
use SixtyNine\Cloud\Factory\PlacerFactory;
use SixtyNine\Cloud\FontMetrics;
use SixtyNine\Cloud\Usher;

class UsherTest extends \PHPUnit_Framework_TestCase
{
    public function testUsher()
    {
        $factory = FontsFactory::create(__DIR__ . '/fixtures/fonts');
        $metrics = new FontMetrics($factory);
        $placer = PlacerFactory::getInstance()->getPlacer('Linear Horizontal', 800, 600, 5);
        $usher = new Usher(800, 600, $placer, $metrics);
        $place = $usher->getPlace('foobar', 'Arial.ttf', 12, 0);
        $place = $usher->getPlace('foobar', 'Arial.ttf', 12, 90);
    }
}
