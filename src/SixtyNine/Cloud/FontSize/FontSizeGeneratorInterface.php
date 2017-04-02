<?php

namespace SixtyNine\Cloud\FontSize;

interface FontSizeGeneratorInterface
{
    /**
     * @param int $count
     * @param int $maxCount
     * @param int $minFontSize
     * @param int $maxFontSize
     * @return int
     */
    function calculateFontSize($count, $maxCount, $minFontSize, $maxFontSize);
}