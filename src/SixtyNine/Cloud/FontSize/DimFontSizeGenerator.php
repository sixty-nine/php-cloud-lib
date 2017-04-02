<?php

namespace SixtyNine\Cloud\FontSize;

class DimFontSizeGenerator implements FontSizeGeneratorInterface
{
    public function calculateFontSize($count, $maxCount, $minFontSize, $maxFontSize)
    {
        $deltaFont = $maxFontSize - $minFontSize;
        return (int)($minFontSize + $deltaFont * pow($count / $maxCount, 2));
    }
}
