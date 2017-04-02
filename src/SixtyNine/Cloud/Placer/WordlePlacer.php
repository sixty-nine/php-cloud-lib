<?php

namespace SixtyNine\Cloud\Placer;

use Imagine\Image\Point;
use Imagine\Image\PointInterface;

class WordlePlacer extends AbstractPlacer
{
    protected $increment = 0;

    public function getNextPlaceToTry(PointInterface $current)
    {
        $i = $this->increment;
        $this->increment += 0.75;

        $x = $current->getX() + ($i / 2 * cos($i));
        $y = $current->getY() + ($i / 2 * sin($i));

        if ($x < 0 || $y < 0) {
            return false;
        }

        return new Point($x, $y);
    }
}
