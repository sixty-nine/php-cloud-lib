<?php

namespace SixtyNine\Cloud\Tests\Builder;

use SixtyNine\Cloud\Builder\CloudBuilder;
use SixtyNine\Cloud\Builder\WordsListBuilder;
use SixtyNine\Cloud\Factory\FontsFactory;
use SixtyNine\Cloud\Model\Cloud;
use PHPUnit\Framework\TestCase;

class CloudBuilderTest extends TestCase
{
    public function testConstructor()
    {
        $factory = FontsFactory::create(__DIR__ . '/../fixtures/fonts');
        $cloud = CloudBuilder::create($factory)
            ->setBackgroundColor('#ffffff')
            ->setDimension(1024, 768)
            ->setFont('Arial.ttf')
            ->build()
        ;
        $this->assertInstanceOf(Cloud::class, $cloud);
        $this->assertEquals('Arial.ttf', $cloud->getFont());
        $this->assertEquals(1024, $cloud->getWidth());
        $this->assertEquals(768, $cloud->getHeight());
        $this->assertEmpty($cloud->getWords());
    }

    public function testAddWords()
    {
        $list = WordsListBuilder::create()->importWords('foobar foo foo bar')->build('foobar');
        $factory = FontsFactory::create(__DIR__ . '/../fixtures/fonts');
        $cloud = CloudBuilder::create($factory)
            ->setFont('Arial.ttf')
            ->useList($list)
            ->build()
        ;

        $this->assertCount(3, $cloud->getWords());
    }
}
