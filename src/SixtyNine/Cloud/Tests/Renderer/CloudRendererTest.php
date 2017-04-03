<?php

namespace SixtyNine\Cloud\Tests\Renderer;

use Imagine\Gd\Image;
use SixtyNine\Cloud\Builder\CloudBuilder;
use SixtyNine\Cloud\Builder\FiltersBuilder;
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
            ->importWords(<<<EOF
Algorithms to detect collision in 2D games depend on the type of shapes that can collide (e.g. Rectangle to Rectangle,
Rectangle to Circle, Circle to Circle). Generally you will have a simple generic shape that covers the entity known as
a "hitbox" so even though collision may not be pixel perfect, it will look good enough and be performant across multiple
entities. This article provides a review of the most common techniques used to provide collision detection in 2D games.
EOF
            )
            ->setFilters(FiltersBuilder::create()
                ->setMinLength(5)
                ->setMaxLength(10)
                ->build()
            )
//            ->setMaxWords(30)
            ->build('foobar')
        ;

        $factory = FontsFactory::create(__DIR__ . '/../fixtures/fonts');

        $placerName = PlacerFactory::PLACER_CIRCULAR;
        $placer = PlacerFactory::getInstance()->getPlacer($placerName, 800, 600);

        $cloud = CloudBuilder::create($factory)
            ->setBackgroundColor('#ffffff')
            ->setDimension(800, 600)
            ->setFont('Arial.ttf')
            ->setPlacer($placerName)
            ->useList($list)
            ->build()
        ;

        $renderer = new CloudRenderer();
        $image = $renderer->render($cloud, $factory, true);
        $renderer->renderUsher($image, $placer, '#FFAA50');

        $this->assertInstanceOf(Image::class, $image);

//        $image->save('/tmp/image.png');
    }
}
