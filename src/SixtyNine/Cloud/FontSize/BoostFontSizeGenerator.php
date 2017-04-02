<?php

namespace SixtyNine\Cloud\FontSize;

class BoostFontSizeGenerator implements FontSizeGeneratorInterface
{
    public function calculateFontSize($count, $maxCount, $minFontSize, $maxFontSize)
    {
        $deltaFont = $maxFontSize - $minFontSize;
        return (int)($minFontSize + $deltaFont * sqrt($count / $maxCount));
    }
}
