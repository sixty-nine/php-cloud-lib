<?php

namespace SixtyNine\Cloud\Placer;

use Imagine\Image\Point;
use Imagine\Image\PointInterface;

class SpiranglePlacer extends AbstractPlacer
{
    /** @var int */
    protected $increment = 0;

    /**
     * @param double $n
     * @return bool
     */
    protected function sign($n) {
        return ($n > 0) - ($n < 0);
    }

    public function getNextPlaceToTry(PointInterface $current)
    {
        $r = 0.1;
        $x = $current->getX() + $this->sign(cos($this->increment)) * $r * $this->increment;
        $y = $current->getY() + $this->sign(sin($this->increment)) * $r / 2 * $this->increment;
        $this->increment += 0.05;

        if ($x < 0 || $y < 0) {
            return false;
        }

        return new Point($x, $y);
    }
}
