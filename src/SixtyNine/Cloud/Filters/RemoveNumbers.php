<?php

namespace SixtyNine\Cloud\Filters;

/**
 * Remove trailing punctuation from words.
 */
class RemoveNumbers extends AbstractFilter implements FilterInterface
{
    /** {@inheritdoc} */
    public function filterWord($word)
    {
        return str_replace(explode(',', '1,2,3,4,5,6,7,8,9,0'), '', $word);
    }
}