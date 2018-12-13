<?php

namespace SixtyNine\Cloud\Tests\Builder;

use Imagine\Gd\Imagine;
use Imagine\Image\Box as ImagineBox;
use Imagine\Image\Color;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;
use SixtyNine\Cloud\Drawer\Drawer;
use SixtyNine\Cloud\Factory\FontsFactory;
use SixtyNine\Cloud\Factory\PlacerFactory;
use SixtyNine\Cloud\FontMetrics;
use SixtyNine\Cloud\Model\Cloud;
use SixtyNine\Cloud\Renderer\CloudRenderer;
use SixtyNine\Cloud\Usher\Usher;
use PHPUnit\Framework\TestCase;

class DrawingTest extends TestCase
{
    public function setUp()
    {
        if (array_key_exists('CI', $_ENV) && $_ENV['CI']) {
            $this->markTestSkipped('Avoid risky tests in CI env');
        }
    }

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

        $image->save('/tmp/test1.png');
    }

    /**
     * Now the same using the Drawer class.
     */
    public function testDrawingWithDrawer()
    {
        $text = 'Foobar';
        $size = 80;

        $factory = FontsFactory::create(__DIR__ . '/fixtures/fonts');
        $metrics = new FontMetrics($factory);
        $fontSize = $metrics->calculateSize($text, 'Arial.ttf', $size, 0);
        $pos = array(200, 200);
        $box = array($fontSize->getWidth(), $fontSize->getHeight());

        $image = Drawer::create($factory)
            ->createImage(400, 400, '#000000')
            ->setFont('Arial.ttf')
            ->drawText($pos[0], $pos[1], $text, $size, '#FFFFFF', 0)
            ->drawText($pos[0], $pos[1], $text, $size, '#FFFFFF', 90)
            ->drawText($pos[0], $pos[1], $text, $size, '#FFFFFF', 180)
            ->drawText($pos[0], $pos[1], $text, $size, '#FFFFFF', 270)
            ->drawBoxForText($pos[0], $pos[1], $box[0], $box[1], 0, '#00ffff')
            ->drawBoxForText($pos[0], $pos[1], $box[0], $box[1], 90, '#00ffff')
            ->drawBoxForText($pos[0], $pos[1], $box[0], $box[1], 180, '#00ffff')
            ->drawBoxForText($pos[0], $pos[1], $box[0], $box[1], 270, '#00ffff')
            ->getImage()
        ;

        $image->save('/tmp/test2.png');
    }

    public function testDrawingAtOrigin()
    {
        $text = 'Foobar';
        $size = 80;

        $factory = FontsFactory::create(__DIR__ . '/fixtures/fonts');
        $metrics = new FontMetrics($factory);
        $fontSize = $metrics->calculateSize($text, 'Arial.ttf', $size, 0);
        $pos = array(0, 0);
        $box = array($fontSize->getWidth(), $fontSize->getHeight());

        $image = Drawer::create($factory)
            ->createImage(400, 400, '#000000')
            ->setFont('Arial.ttf')
            ->drawText($pos[0], $pos[1], $text, $size, '#FFFFFF', 0)
            ->drawBoxForText($pos[0], $pos[1], $box[0], $box[1], 0, '#00ffff')
            ->getImage()
        ;

        $image->save('/tmp/test3.png');
    }

    public function testDrawingWithUsher()
    {
        $factory = FontsFactory::create(__DIR__ . '/fixtures/fonts');
        $metrics = new FontMetrics($factory);
        $placer = PlacerFactory::getInstance()->getPlacer(PlacerFactory::PLACER_CIRCULAR, 400, 400, 5);
        $usher = new Usher(400, 400, $placer, $metrics);
        $drawer = Drawer::create($factory)
            ->createImage(400, 400, '#000000')
            ->setFont('Arial.ttf')
        ;

        $words = array(
            array('first', 50, 0, '#ff0000'),
            array('second', 20, 270, '#0000ff'),
            array('third', 24, 0, '#00ff00'),
            array('fourth', 36, 270, '#00ffff'),
            array('fifth', 36, 0, '#ff00ff'),
            array('sixth', 20, 0, '#ffff00'),
            array('seventh', 24, 270, '#aaaaaa'),
        );

        foreach ($words as $values) {
            $place = $usher->getPlace($values[0], 'Arial.ttf', $values[1], $values[2]);
            if ($place) {
                if ($values[2] === 0) {
                    $drawer->drawText($place->getX(), $place->getY(), $values[0], $values[1], $values[3], $values[2]);
                } else {
                    $drawer->drawText($place->getX() + $place->getWidth(), $place->getY() + $place->getHeight() - $values[1], $values[0], $values[1], $values[3], $values[2]);
                }
                $drawer->drawBox($place->getX(), $place->getY(), $place->getWidth(), $place->getHeight(), $values[3]);
            }
        }

        $drawer->drawMask($usher->getMask(), '#ffffff');

        $drawer->getImage()->save('/tmp/test4.png');
    }

    public function testDrawingWithPreciseUsher()
    {
        $factory = FontsFactory::create(__DIR__ . '/fixtures/fonts');
        $metrics = new FontMetrics($factory);
        $placer = PlacerFactory::getInstance()->getPlacer(PlacerFactory::PLACER_CIRCULAR, 400, 400, 5);
        $usher = new Usher(400, 400, $placer, $metrics, true);
        $drawer = Drawer::create($factory)
            ->createImage(400, 400, '#000000')
            ->setFont('Arial.ttf')
        ;

        $words = array(
            array('first', 50, 0, '#ff0000'),
            array('second', 20, 270, '#0000ff'),
            array('third', 24, 0, '#00ff00'),
            array('fourth', 36, 270, '#00ffff'),
            array('fifth', 36, 0, '#ff00ff'),
            array('sixth', 40, 0, '#ffff00'),
            array('seventh', 24, 270, '#aaaaaa'),
        );

        foreach ($words as $values) {
            $place = $usher->getPlace($values[0], 'Arial.ttf', $values[1], $values[2]);
            if ($place) {
                if ($values[2] === 0) {
                    $drawer->drawText($place->getX(), $place->getY(), $values[0], $values[1], $values[3], $values[2]);
                } else {
                    $drawer->drawText($place->getX() + $place->getWidth(), $place->getY() + $place->getHeight() - $values[1], $values[0], $values[1], $values[3], $values[2]);
                }
                $drawer->drawBox($place->getX(), $place->getY(), $place->getWidth(), $place->getHeight(), $values[3]);
            }
        }

        $drawer->drawMask($usher->getMask(), '#ffffff');

        $drawer->getImage()->save('/tmp/test5.png');
    }

    /**
     * @param ImageInterface $image
     * @param PointInterface $pos
     * @param int $width
     * @param int $height
     * @param string $color
     */
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

    /**
     * @param int $x
     * @param int $y
     * @return Point
     */
    protected function createPoint($x, $y)
    {
        $x = $x >= 0 ? $x : 0;
        $y = $y >= 0 ? $y : 0;
        return new Point($x, $y);
    }
}
