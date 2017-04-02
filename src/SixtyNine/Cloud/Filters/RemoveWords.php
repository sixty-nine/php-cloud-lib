<?php

namespace SixtyNine\Cloud\Filters;

/**
 * Remove words in a blacklist.
 */
class RemoveWords extends AbstractFilter implements FilterInterface
{
    protected $unwantedWords;

    /**
     * @param string[] $unwantedWords Array of words to be removed.
     */
    public function __construct($unwantedWords = array(
        'and', 'our', 'your', 'their', 'his', 'her', 'the', 'you', 'them', 'yours',
        'with', 'such', 'even')
    )
    {
        $this->unwantedWords = $unwantedWords;
    }

    /** {@inheritdoc} */
    public function filterWord($word)
    {
        if (in_array($word, $this->unwantedWords))  {
            return false;
        }
        return $word;
    }

    /** {@inheritdoc} */
    public function keepWord($word)
    {
        return !in_array($word, $this->unwantedWords);
    }
}