<?php

namespace SixtyNine\Cloud\Tests\Model;

use SixtyNine\Cloud\Model\WordsList;

class WordsListTest extends \PHPUnit_Framework_TestCase
{
    public function testImportWords()
    {
        $list = new WordsList();
        $list->importWords(file_get_contents(__DIR__ . '/../fixtures/lorem.txt'));
        $this->assertEquals(100, $list->getWordsCount());
    }

}

