<?php

namespace SixtyNine\Cloud\Placer;

use Imagine\Image\Point;
use Imagine\Image\PointInterface;

class LinearVerticalPlacer extends AbstractPlacer
{
    public function getNextPlaceToTry(PointInterface $current)
    {
        $increment = 10;

        if ($current->getY() < $this->imgHeight) {
            return new Point($current->getX(), $current->getY() + $increment);
        }

        if ($current->getX() < $this->imgWidth) {
            return new Point($current->getX() + $increment, 0);
        }

        return false;
    }

    function getFirstPlaceToTry()
    {
        return new Point(0, 0);
    }

}
