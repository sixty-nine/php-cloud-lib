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
    public function __construct($punctuation = array('.', ',', ';', '?', '!', '{' , '}', '[', ']'))
    {
        $this->punctuation = $punctuation;
    }

    /** {@inheritdoc} */
    public function filterWord($word)
    {
        $punctuation = implode('|', array_map(function ($p) {
            return preg_quote($p, '/');
        }, $this->punctuation));

        // null on invalid UTF-8: keep the word as it is
        $filtered = preg_replace('/(?:' . $punctuation . ')+$/u', '', $word);

        return $filtered === null ? $word : $filtered;
    }
}