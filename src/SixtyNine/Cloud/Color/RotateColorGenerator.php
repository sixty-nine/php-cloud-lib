<?php

namespace SixtyNine\Cloud\Color;

class RotateColorGenerator extends ColorGenerator
{
    protected $current = 0;

    /**
     * @return string
     */
    public function getNextColor()
    {
        $colors = $this->palette->getColors();
        $color = $colors[$this->current % count($colors)];
        $this->current++;
        return $color;
    }
}
