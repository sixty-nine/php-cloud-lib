<?php


namespace SixtyNine\Cloud\Tests\Filters;


use SixtyNine\Cloud\Filters\FilterInterface;
use SixtyNine\Cloud\Filters\Filters;
use SixtyNine\Cloud\Filters\RemoveNumbers;
use SixtyNine\Cloud\Filters\RemoveWords;
use SixtyNine\Cloud\Model\Word;

class FiltersTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $filter
     * @param $word
     * @param $expectedKeepWord
     * @param null $expectedWord
     * @dataProvider wordsProvider
     */
    public function testTest($filter, $word, $expectedKeepWord, $expectedWord = null)
    {
        $this->assertFilteredWord($filter, $word, $expectedKeepWord, $expectedWord);
    }

    public function wordsProvider()
    {
        return array(
            [
                new RemoveWords(array('and')),
                'foobar',
                true
            ],
            [
                new RemoveWords(array('and')),
                'and',
                false
            ],
            [
                new RemoveNumbers(),
                '1234abcd5678',
                true,
                'abcd'
            ],
            [
                new RemoveNumbers(),
                '1234567890',
                true,
                ''
            ],
        );
    }

    protected function assertFilteredWord(FilterInterface $filter, $word, $expectedKeepWord, $expectedFiltered = null)
    {
        $filters = new Filters(array($filter));
        $filtered = $filters->apply($word);
        $filteredWord = $filter->filterWord($word);

        if ($expectedKeepWord) {
            if ($filtered !== '') {
                $this->assertTrue((bool)$filtered);
                $this->assertTrue((bool)$filteredWord);
            } else {
                $this->assertEquals('', $filtered);
                $this->assertEquals('', $filteredWord);
            }
        } else {
            $this->assertFalse($filtered, 'Expected the word to be filtered out');
            $this->assertFalse($filter->keepWord($word));
        }

        if ($expectedFiltered) {
            $this->assertEquals($expectedFiltered, $filtered, 'Unexpected filtered value');
            $this->assertEquals($expectedFiltered, $filteredWord, 'Unexpected filtered word');
        }
    }

    protected function getWord($text)
    {
        return (new Word())->setText($text);
    }
}
 