<?php
/**
 * This file is part of the sixty-nine/word-cloud.
 * Copyright (c) 2010-2016 Daniele Barsotti<sixtynine.db@gmail.com>
 */

namespace SixtyNine\Cloud\Filters;

abstract class AbstractFilter implements FilterInterface
{
    /** {@inheritdoc} */
    public function keepWord($word)
    {
        return true;
    }

    /** {@inheritdoc} */
    public function filterWord($word)
    {
        return $word;
    }
}
