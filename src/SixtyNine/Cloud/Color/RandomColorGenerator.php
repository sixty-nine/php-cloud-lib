<?php

namespace SixtyNine\Cloud\Color;

class RandomColorGenerator extends ColorGenerator
{
    /**
     * @return string
     */
    public function getNextColor()
    {
        $colors = $this->palette->getColors();
        return $colors[rand(0, count($colors) - 1)];
    }
}