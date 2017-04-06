<?php

namespace SixtyNine\Cloud\Placer;

use Imagine\Image\Point;
use Imagine\Image\PointInterface;

class LinearHorizontalPlacer extends AbstractPlacer
{
    /** @var int */
    protected $increment;

    /**
     * @param int $imgWidth
     * @param int $imgHeight
     * @param int $increment
     */
    public function __construct($imgWidth, $imgHeight, $increment = 10)
    {
        parent::__construct($imgWidth, $imgHeight);
        $this->increment = $increment;
    }

    /**
     * @param PointInterface $current
     * @return bool|PointInterface
     */
    public function getNextPlaceToTry(PointInterface $current)
    {
        if ($current->getX() < $this->imgWidth) {
            return new Point($current->getX() + $this->increment, $current->getY());
        }

        if ($current->getY() < $this->imgHeight) {
            return new Point(0, $current->getY() + $this->increment);
        }

        return false;
    }

    /**
     * @return PointInterface
     */
    function getFirstPlaceToTry()
    {
        return new Point(0, 0);
    }

}
