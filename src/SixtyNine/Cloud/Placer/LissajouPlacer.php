<?php

namespace SixtyNine\Cloud\Placer;

use Imagine\Image\Point;
use Imagine\Image\PointInterface;

class LissajouPlacer extends AbstractPlacer
{
    protected $increment = 0;

    public function getNextPlaceToTry(PointInterface $current)
    {
        $first = $this->getFirstPlaceToTry();
        // TODO: needs some params adjusting ($a, $b, $delta)
        $a = 50;
        $b = 49;
        $A = $first->getX();
        $B = $first->getY();
        $delta = pi() / 4;
        $this->increment += 0.01;

        $x = $A * sin($a * $this->increment + $delta) + $A;
        $y = $B * sin($b * $this->increment) + $B;

        if ($this->increment > 2 * pi()) {
            return false;
        }

        return new Point($x, $y);
    }

    /** {@inheritdoc} */
    function getFirstPlaceToTry()
    {
        return new Point($this->imgWidth / 2, $this->imgHeight / 2);
    }
}
