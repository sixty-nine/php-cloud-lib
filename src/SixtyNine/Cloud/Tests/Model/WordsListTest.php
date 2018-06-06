<?php

namespace SixtyNine\Cloud\Tests\Model;

use SixtyNine\Cloud\Model\WordsList;
use PHPUnit\Framework\TestCase;

class WordsListTest extends TestCase
{
    public function testImportWords()
    {
        $list = new WordsList();
        $list->importWords(file_get_contents(__DIR__ . '/../fixtures/lorem.txt'));
        $this->assertEquals(100, $list->getWordsCount());
    }

}

