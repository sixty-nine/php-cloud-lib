<?php

namespace SixtyNine\Cloud\Filters;

/**
 * Remove unwanted characters from words.
 */
class RemoveCharacters extends AbstractFilter implements FilterInterface
{
    protected $unwantedCharacters;

    /**
     * @param array $unwantedCharacters Array of characters to be removed.
     */
    public function __construct($unwantedCharacters = null)
    {
        if (!$unwantedCharacters) {
            $unwantedCharacters = array(
                ':', '?', '!', '\'', '"', '(', ')', '[', ']',
            );
        }
        $this->unwantedCharacters = $unwantedCharacters;
    }

    /** {@inheritdoc} */
    public function filterWord($word)
    {
        foreach($this->unwantedCharacters as $p) {
            $word = str_replace($p, '', $word);
        }

        return $word;
    }
}