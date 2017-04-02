<?php

namespace SixtyNine\Cloud\Filters;

/**
 * Remove trailing punctuation from words.
 */
class RemoveTrailingCharacters extends AbstractFilter implements FilterInterface
{
    protected $punctuation;

    /**
     * @param string[] $punctuation Array of punctuation to be removed.
     */
    public function __construct($punctuation = array('.', ',', ';', '?', '!'))
    {
        $this->punctuation = $punctuation;
    }

    /** {@inheritdoc} */
    public function filterWord($word)
    {
        foreach($this->punctuation as $p) {
            if(substr($word, -1) == $p) {
                $word = substr($word, 0, -1);
            }
        }

        return $word;
    }
}