<?php

namespace SixtyNine\Cloud\Tests\Model;

use Imagine\Gd\Image;
use Imagine\Image\Point;
use SixtyNine\Cloud\Model\Box;
use SixtyNine\Cloud\Usher;

class BoxTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $box = new Box(1, 2, 3, 4);
        $this->assertInstanceOf(Box::class, $box);
        $this->assertAttributeEquals(1, 'x', $box);
        $this->assertAttributeEquals(2, 'y', $box);
        $this->assertAttributeEquals(3, 'width', $box);
        $this->assertAttributeEquals(4, 'height', $box);
        $this->assertAttributeEquals(1, 'left', $box);
        $this->assertAttributeEquals(4, 'right', $box);
        $this->assertAttributeEquals(2, 'top', $box);
        $this->assertAttributeEquals(6, 'bottom', $box);
    }

    public function testConstructFromImagine()
    {
        $p = new Point(10, 20);
        $b = new \Imagine\Image\Box(100, 200);
        $box = Box::constructFromImagine($p, $b);

        $this->assertInstanceOf(Box::class, $box);
        $this->assertAttributeEquals(10, 'x', $box);
        $this->assertAttributeEquals(20, 'y', $box);
        $this->assertAttributeEquals(100, 'width', $box);
        $this->assertAttributeEquals(200, 'height', $box);
        $this->assertAttributeEquals(10, 'left', $box);
        $this->assertAttributeEquals(110, 'right', $box);
        $this->assertAttributeEquals(20, 'top', $box);
        $this->assertAttributeEquals(220, 'bottom', $box);
    }

    /**
     * @dataProvider boxesProvider
     */
    public function testIntersect(Box $b1, Box $b2, $shouldCollide)
    {
        $collide = $b1->intersects($b2);

        if ($collide && !$shouldCollide) {
            $this->fail('Collision not expected');
        }

        if (!$collide && $shouldCollide) {
            $this->fail('Collision expected');
        }
    }

    /**
     * @return array
     */
    public function boxesProvider()
    {
        return array(
            array(new Box(1, 1, 1, 1), new Box(2, 2, 1, 1), false),
            array(new Box(1, 1, 1, 1), new Box(1, 1, 1, 1), true),
            array(new Box(10, 10, 100, 50), new Box(5, 5, 5, 50), false),
            array(new Box(10, 10, 100, 50), new Box(5, 5, 50, 50), true),
            array(new Box(0, 10, 100, 10), new Box(10, 0, 10, 100), true),
        );
    }
}
