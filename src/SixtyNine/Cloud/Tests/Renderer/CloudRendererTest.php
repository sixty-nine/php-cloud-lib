<?php

namespace SixtyNine\Cloud\Tests\Renderer;

use Imagine\Gd\Image;
use SixtyNine\Cloud\Builder\CloudBuilder;
use SixtyNine\Cloud\Builder\PalettesBuilder;
use SixtyNine\Cloud\Builder\WordsListBuilder;
use SixtyNine\Cloud\Color\RandomColorGenerator;
use SixtyNine\Cloud\Factory\FontsFactory;
use SixtyNine\Cloud\Factory\PlacerFactory;
use SixtyNine\Cloud\Renderer\CloudRenderer;

class CloudRendererTest extends \PHPUnit_Framework_TestCase
{
    public function testRender()
    {
        $colorGenerator = new RandomColorGenerator(PalettesBuilder::create()->getRandomPalette());

        $list = WordsListBuilder::create()
            ->randomizeOrientation(50)
            ->randomizeColors($colorGenerator)
            ->importWords('foobar foo foo bar')
            ->build('foobar')
        ;

        $factory = FontsFactory::create(__DIR__ . '/../fixtures/fonts');

        $placerName = 'Circular';
        $placer = PlacerFactory::getInstance()->getPlacer($placerName, 800, 600);

        $cloud = CloudBuilder::create($factory)
            ->setBackgroundColor('#ffffff')
            ->setDimension(800, 600)
            ->setFont('Arial.ttf')
            ->setPlacer($placerName)
            ->useList($list)
            ->build()
        ;

        $this->assertCount(3, $cloud->getWords());

        $renderer = new CloudRenderer();
        $image = $renderer->render($cloud, $factory, true);
        $renderer->renderUsher($image, $placer, '#FFAA50');

        $this->assertInstanceOf(Image::class, $image);

//        $image->save('/tmp/image.png');
    }
}
