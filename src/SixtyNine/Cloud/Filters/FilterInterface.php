<?php

namespace SixtyNine\Cloud\Filters;

interface FilterInterface
{
    /**
     * @param string $word The word to filter
     * @return bool
     */
    function keepWord($word);

    /**
     * @param string $word
     * @return string
     */
    function filterWord($word);
}
