<?php

namespace SixtyNine\Cloud\Tests\Builder;

use SixtyNine\Cloud\Builder\FiltersBuilder;
use SixtyNine\Cloud\Builder\WordsListBuilder;
use SixtyNine\Cloud\Model\Word;
use SixtyNine\Cloud\Model\WordsList;
use PHPUnit\Framework\TestCase;

class WordsListBuilderTest extends TestCase
{
    public function testConstructor()
    {
        $list = WordsListBuilder::create()->build('foobar');
        $this->assertInstanceOf(WordsList::class, $list);
        $this->assertEquals('foobar', $list->getName());
        $this->assertEmpty($list->getWords());
    }

    public function testImportWords()
    {
        $list = WordsListBuilder::create()->importWords('foobar foo foo bar')->build('foobar');
        $this->assertInstanceOf(WordsList::class, $list);
        $this->assertEquals('foobar', $list->getName());
        $this->assertCount(3, $list->getWords());

        $word = $list->getWords()->first();
        $this->assertInstanceOf(Word::class, $word);
        $this->assertEquals('foobar', $word->getText());
        $this->assertEquals(1, $word->getCount());

        $word = $list->getWords()->next();
        $this->assertInstanceOf(Word::class, $word);
        $this->assertEquals('foo', $word->getText());
        $this->assertEquals(2, $word->getCount());

        $word = $list->getWords()->next();
        $this->assertInstanceOf(Word::class, $word);
        $this->assertEquals('bar', $word->getText());
        $this->assertEquals(1, $word->getCount());
    }

    public function testImportUrl()
    {
        $min = 5;
        $max = 8;
        $list = WordsListBuilder::create()
            ->setFilters(FiltersBuilder::create()->setMinLength($min)->setMaxLength($max)->build())
            ->importUrl('https://en.wikipedia.org/wiki/Tag_cloud')
            ->build('foobar')
        ;
        $this->assertInstanceOf(WordsList::class, $list);
        $this->assertEquals('foobar', $list->getName());
        $this->assertNotEmpty($list->getWords());

        foreach ($list->getWords() as $word) {
            $len = strlen($word->getText());
            $this->assertTrue($len > $min);
            $this->assertTrue($len < $max);
        }
    }
}
