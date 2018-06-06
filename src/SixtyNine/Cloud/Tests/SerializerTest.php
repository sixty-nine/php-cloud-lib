<?php

namespace SixtyNine\Cloud\Tests\Builder;

use Doctrine\Common\Collections\ArrayCollection;
use SixtyNine\Cloud\Builder\CloudBuilder;
use SixtyNine\Cloud\Builder\PalettesBuilder;
use SixtyNine\Cloud\Builder\WordsListBuilder;
use SixtyNine\Cloud\Color\RandomColorGenerator;
use SixtyNine\Cloud\Factory\FontsFactory;
use SixtyNine\Cloud\Model\Cloud;
use SixtyNine\Cloud\Model\CloudWord;
use SixtyNine\Cloud\Model\Word;
use SixtyNine\Cloud\Model\WordsList;
use SixtyNine\Cloud\Serializer;
use SixtyNine\DataTypes\Box;
use PHPUnit\Framework\TestCase;

class SerializerTest extends TestCase
{
    public function testSaveLoadList()
    {
        $colorGenerator = new RandomColorGenerator(PalettesBuilder::create()->getRandomPalette());

        $list = WordsListBuilder::create()
            ->randomizeOrientation(50)
            ->randomizeColors($colorGenerator)
            ->importWords('foobar foo foo bar')
            ->sort(WordsList::SORT_COUNT, WordsList::SORT_DESC)
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

    public function testLoadWordsList()
    {
        $serializer = new Serializer();
        $list = $serializer->loadList(file_get_contents(__DIR__ . '/fixtures/wordlist.json'));

        $this->assertInstanceOf(WordsList::class, $list);
        $this->assertEquals('foobar', $list->getName());
        $this->assertEquals(2, $list->getWordsCount());
        $this->assertEquals(20, $list->getWordsMaxCount());

        /** @var Word $word */
        $word = $list->getWords()->first();

        $this->assertInstanceOf(Word::class, $word);
        $this->assertEquals('foo', $word->getText());
        $this->assertEquals(10, $word->getCount());
        $this->assertEquals('#000000', $word->getColor());
        $this->assertEquals(1, $word->getPosition());

        $word = $list->getWords()->next();

        $this->assertInstanceOf(Word::class, $word);
        $this->assertEquals('bar', $word->getText());
        $this->assertEquals(20, $word->getCount());
        $this->assertEquals('#ffffff', $word->getColor());
        $this->assertEquals(0, $word->getPosition());
    }

    public function testLoadCloud()
    {
        $serializer = new Serializer();
        $cloud = $serializer->loadCloud(file_get_contents(__DIR__ . '/fixtures/cloud.json'));

        $this->assertInstanceOf(Cloud::class, $cloud);
        $this->assertEquals('#ffffff', $cloud->getBackgroundColor());
        $this->assertEquals(800, $cloud->getWidth());
        $this->assertEquals(600, $cloud->getHeight());
        $this->assertEquals('Arial.ttf', $cloud->getFont());

        $words = $cloud->getWords();

        $this->assertInstanceOf(ArrayCollection::class, $words);
        $this->assertCount(2, $words);

        /** @var CloudWord $word */
        $word = $words->first();
        $this->assertInstanceOf(CloudWord::class, $word);
        $this->assertEquals('foo', $word->getText());
        $this->assertEquals(60, $word->getSize());
        $this->assertEquals(0, $word->getAngle());
        $this->assertEquals('#8c4b47', $word->getColor());
        $this->assertEquals(true, $word->getIsVisible());
        $this->assertTrue(is_array($word->getPosition()));
        $this->assertEquals(array(400, 300), $word->getPosition());
        $this->assertInstanceOf(Box::class, $word->getBox());
        $this->assertEquals(400, $word->getBox()->getX());
        $this->assertEquals(300, $word->getBox()->getY());
        $this->assertEquals(112, $word->getBox()->getWidth());
        $this->assertEquals(61, $word->getBox()->getHeight());

        $word = $words->next();
        $this->assertInstanceOf(CloudWord::class, $word);
        $this->assertEquals('bar', $word->getText());
        $this->assertEquals(35, $word->getSize());
        $this->assertEquals(90, $word->getAngle());
        $this->assertEquals('#8c4b47', $word->getColor());
        $this->assertEquals(true, $word->getIsVisible());
        $this->assertTrue(is_array($word->getPosition()));
        $this->assertEquals(array(389, 263), $word->getPosition());
        $this->assertInstanceOf(Box::class, $word->getBox());
        $this->assertEquals(389, $word->getBox()->getX());
        $this->assertEquals(263, $word->getBox()->getY());
        $this->assertEquals(68, $word->getBox()->getWidth());
        $this->assertEquals(36, $word->getBox()->getHeight());
    }
}
