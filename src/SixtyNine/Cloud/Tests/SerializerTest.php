<?php

namespace SixtyNine\Cloud\Tests\Builder;

use SixtyNine\Cloud\Builder\CloudBuilder;
use SixtyNine\Cloud\Builder\PalettesBuilder;
use SixtyNine\Cloud\Builder\WordsListBuilder;
use SixtyNine\Cloud\Color\RandomColorGenerator;
use SixtyNine\Cloud\Factory\FontsFactory;
use SixtyNine\Cloud\Serializer;

class SerializerTest extends \PHPUnit_Framework_TestCase
{
    public function testSaveLoadList()
    {
        $colorGenerator = new RandomColorGenerator(PalettesBuilder::create()->getRandomPalette());

        $list = WordsListBuilder::create()
            ->randomizeOrientation(50)
            ->randomizeColors($colorGenerator)
            ->importWords('foobar foo foo bar')
            ->build('foobar')
        ;

        $serializer = new Serializer();
        $json = $serializer->saveList($list, true);

        $obj = $serializer->loadList($json);

        $this->assertEquals($list, $obj);
    }

    public function testSaveLoadCloud()
    {
        $colorGenerator = new RandomColorGenerator(PalettesBuilder::create()->getRandomPalette());

        $list = WordsListBuilder::create()
            ->randomizeOrientation(50)
            ->randomizeColors($colorGenerator)
            ->importWords('foobar foo foo bar')
            ->build('foobar')
        ;

        $factory = FontsFactory::create(__DIR__ . '/fixtures/fonts');
        $cloud = CloudBuilder::create($factory)
            ->setFont('Arial.ttf')
            ->useList($list)
            ->build()
        ;

        $serializer = new Serializer();
        $json = $serializer->saveCloud($cloud, true);

        $obj = $serializer->loadCloud($json);

        $this->assertEquals($cloud, $obj);
    }
}
