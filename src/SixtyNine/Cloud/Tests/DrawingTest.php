<?php

namespace SixtyNine\Cloud\Tests\Builder;

use Imagine\Gd\Imagine;
use Imagine\Image\Box as ImagineBox;
use Imagine\Image\Color;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;
use SixtyNine\Cloud\Factory\FontsFactory;
use SixtyNine\Cloud\FontMetrics;

class DrawingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Illustrate the difficulty to properly draw a bounding box around rotated text with Imagine.
     */
    public function testDrawing()
    {
        $imagine = new Imagine();
        $image = $imagine->create(new ImagineBox(400, 400), new Color('#000000'));

        $text = 'Foobar';
        $size = 80;

        $factory = FontsFactory::create(__DIR__ . '/fixtures/fonts');
        $metrics = new FontMetrics($factory);
        $font = $factory->getImagineFont('Arial.ttf', $size, '#ffffff');

        $fontSize = $metrics->calculateSize($text, 'Arial.ttf', $size, 0);
        $pos = array(200, 200);
        $box = array($fontSize->getWidth(), $fontSize->getHeight());

        $image->draw()->text($text, $font, new Point($pos[0], $pos[1]), 0);
        $image->draw()->text($text, $font, new Point($pos[0], $pos[1]), 90);
        $image->draw()->text($text, $font, new Point($pos[0], $pos[1]), 180);
        $image->draw()->text($text, $font, new Point($pos[0], $pos[1]), 270);

        // Angle = 0 - so far so good...
        $this->drawBox($image, $this->createPoint($pos[0], $pos[1]), $box[0], $box[1], 0xFF0000);

        // Angle = 90 - Y needs to be adjusted, dimensions inverted
        $this->drawBox($image, $this->createPoint($pos[0], $pos[1] + $box[1]), $box[1], $box[0], 0xFF0000);

        // Angle = 180 - X and Y need adjustments, X might go out of the image on the left (which Imagine does not like)
        $x = $pos[0] - $box[0];
        $width = $x >= 0 ? $box[0] : $box[0] + $x;
        $this->drawBox($image, $this->createPoint($x, $pos[1] + $box[1]), $width, $box[1], 0xFF0000);

        // Angle = 270 - X and Y need adjustments, it depends on the font size, Y might go out of the image on the top, dimensions inverted
        $y = $pos[1] - $box[0] + $size;
        $height = $y >= 0 ? $box[0] : $box[0] + $y;
        $this->drawBox($image, $this->createPoint($pos[0] - $box[1], $y), $box[1], $height, 0xFF0000);

        $image->save('/tmp/test.png');
    }

    protected function drawBox(ImageInterface $image, PointInterface $pos, $width, $height, $color = '#ffffff')
    {
        $x = $pos->getX();
        $y = $pos->getY();

        $points = array(
            $this->createPoint($x, $y),
            $this->createPoint($x + $width, $y),
            $this->createPoint($x + $width, $y + $height),
            $this->createPoint($x, $y + $height),
        );
        $image->draw()->polygon($points, new Color($color));
    }

    protected function createPoint($x, $y)
    {
        $x = $x >= 0 ? $x : 0;
        $y = $y >= 0 ? $y : 0;
        return new Point($x, $y);
    }
}
