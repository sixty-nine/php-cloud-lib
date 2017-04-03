<?php

namespace SixtyNine\Cloud\Tests\Renderer;

use Imagine\Image\Point;
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
        $placer = PlacerFactory::getInstance()->getPlacer(PlacerFactory::PLACER_LINEAR_H, 800, 600, 5);
        $usher = new Usher(800, 600, $placer, $metrics);
        $place1 = $usher->getPlace('foobar', 'Arial.ttf', 12, 0);
        // Assert the first word is positioned at (0,0)
        $this->assertEquals(new Point(0, 0), $place1->getPosition());

        $place2 = $usher->getPlace('foobar', 'Arial.ttf', 12, 90);
        // Assert the second word is placed on the right of the first word
        $this->assertEquals(0, $place2->getY());
        $this->assertTrue($place1->getWidth() < $place2->getX());
    }
}
