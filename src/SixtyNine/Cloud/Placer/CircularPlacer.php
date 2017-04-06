<?php

namespace SixtyNine\Cloud\Placer;

use Imagine\Image\Point;
use Imagine\Image\PointInterface;

class CircularPlacer extends AbstractPlacer
{
    protected $increment = 0;

    /** {@inheritdoc} */
    public function getNextPlaceToTry(PointInterface $current)
    {
        $a = 0;
        $b = 0.05;
        $i = $this->increment;
        $this->increment += 0.05;
        $r = $a + $b * $i;

        $x = $current->getX() + cos($i) * $r;
        $y = $current->getY() + sin($i) * $r;

        if ($x < 0 || $y < 0) {
            return false;
        }

        return new Point($x, $y);
    }
}
