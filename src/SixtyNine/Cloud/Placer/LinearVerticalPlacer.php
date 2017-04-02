<?php

namespace SixtyNine\Cloud\Placer;

use Imagine\Image\Point;
use Imagine\Image\PointInterface;

class LinearVerticalPlacer extends AbstractPlacer
{
    /** @var int */
    protected $increment;

    public function __construct($imgWidth, $imgHeight, $increment = 10)
    {
        parent::__construct($imgWidth, $imgHeight);
        $this->increment = $increment;
    }

    public function getNextPlaceToTry(PointInterface $current)
    {
        if ($current->getY() < $this->imgHeight) {
            return new Point($current->getX(), $current->getY() + $this->increment);
        }

        if ($current->getX() < $this->imgWidth) {
            return new Point($current->getX() + $this->increment, 0);
        }

        return false;
    }

    function getFirstPlaceToTry()
    {
        return new Point(0, 0);
    }

}
