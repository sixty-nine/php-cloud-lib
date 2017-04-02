<?php

namespace SixtyNine\Cloud\Tests\Renderer;

use Imagine\Gd\Image;
use SixtyNine\Cloud\Builder\CloudBuilder;
use SixtyNine\Cloud\Builder\PalettesBuilder;
use SixtyNine\Cloud\Builder\WordsListBuilder;
use SixtyNine\Cloud\Color\RandomColorGenerator;
use SixtyNine\Cloud\Factory\FontsFactory;
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

        $cloud = CloudBuilder::create($factory)
            ->setBackgroundColor('#ffffff')
            ->setFont('Arial.ttf')
            ->useList($list)
            ->build()
        ;

        $this->assertCount(3, $cloud->getWords());

        $renderer = new CloudRenderer();
        $image = $renderer->render($cloud, $factory);

        $this->assertInstanceOf(Image::class, $image);

//        $image->save('/tmp/image.png');
    }
}
