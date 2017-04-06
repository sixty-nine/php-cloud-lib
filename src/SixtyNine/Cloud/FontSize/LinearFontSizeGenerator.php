<?php

namespace SixtyNine\Cloud\FontSize;

class LinearFontSizeGenerator implements FontSizeGeneratorInterface
{
    /**
     * @param int $count
     * @param int $maxCount
     * @param int $minFontSize
     * @param int $maxFontSize
     * @return int
     */
    public function calculateFontSize($count, $maxCount, $minFontSize, $maxFontSize)
    {
        $deltaFont = $maxFontSize - $minFontSize;
        return $minFontSize + ($deltaFont / $maxCount * $count);
    }
}
