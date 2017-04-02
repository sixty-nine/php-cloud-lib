<?php

namespace SixtyNine\Cloud\Placer;

use Imagine\Image\Point;
use Imagine\Image\PointInterface;

class LinearHorizontalPlacer extends AbstractPlacer
{
    public function getNextPlaceToTry(PointInterface $current)
    {
        $increment = 10;

        if ($current->getX() < $this->imgWidth) {
            return new Point($current->getX() + $increment, $current->getY());
        }

        if ($current->getY() < $this->imgHeight) {
            return new Point(0, $current->getY() + $increment);
        }

        return false;
    }

    function getFirstPlaceToTry()
    {
        return new Point(0, 0);
    }

}
