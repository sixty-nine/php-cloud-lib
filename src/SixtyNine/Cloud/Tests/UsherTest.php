<?php

namespace SixtyNine\Cloud\Tests\Renderer;

use Imagine\Gd\Image;
use SixtyNine\Cloud\Factory\FontsFactory;
use SixtyNine\Cloud\Factory\PlacerFactory;
use SixtyNine\Cloud\Usher;

class UsherTest extends \PHPUnit_Framework_TestCase
{
    public function testUsher()
    {
        $factory = FontsFactory::create(__DIR__ . '/fixtures/fonts');
        $placer = PlacerFactory::getInstance()->getPlacer('Linear Horizontal', 800, 600, 5);
        $usher = new Usher(800, 600, $placer);
        $font = $factory->getImagineFont('Arial.ttf', 12);
        $place = $usher->getPlace($font->box('foobar', 0));
//        var_dump($place, $font->box('foobar', 0));
        $place = $usher->getPlace($font->box('foobar', 90));
//        var_dump($place, $font->box('foobar', 90));
    }
}
