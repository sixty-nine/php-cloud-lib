<?php

namespace SixtyNine\Cloud\Filters;

/**
 * Remove trailing punctuation from words.
 */
class RemoveFrontCharacters extends AbstractFilter implements FilterInterface
{
    protected $punctuation;

    /**
     * @param array $punctuation Array of punctuation to be removed.
     */
    public function __construct($punctuation = array('\''))
    {
        $this->punctuation = $punctuation;
    }

    /** {@inheritdoc} */
    public function filterWord($word)
    {
        foreach($this->punctuation as $p) {
            if(substr($word, 1, 1) === $p) {
                $word = substr($word, 2);
            }
        }

        return $word;
    }
}