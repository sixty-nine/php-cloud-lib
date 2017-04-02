<?php

namespace SixtyNine\Cloud\Color;

use SixtyNine\Cloud\Model\Palette;

abstract class ColorGenerator implements ColorGeneratorInterface
{
    /** @var \SixtyNine\Cloud\Model\Palette */
    protected $palette;

    public function __construct(Palette $palette)
    {
        $this->palette = $palette;
    }

    public abstract function getNextColor();
}
