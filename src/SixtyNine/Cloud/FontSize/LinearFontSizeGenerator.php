<?php

namespace SixtyNine\Cloud\FontSize;

class LinearFontSizeGenerator implements FontSizeGeneratorInterface
{
    public function calculateFontSize($count, $maxCount, $minFontSize, $maxFontSize)
    {
        $deltaFont = $maxFontSize - $minFontSize;
        return $minFontSize + ($deltaFont / $maxCount * $count);
    }
}
