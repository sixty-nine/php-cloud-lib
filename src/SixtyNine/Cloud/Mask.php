<?php

namespace SixtyNine\Cloud;


use Imagine\Image\BoxInterface;
use Imagine\Image\PointInterface;

class Mask
{
    private $drawnBoxes = array();

    /** {@inheritdoc} */
    public function add(PointInterface $pos, BoxInterface $box)
    {
        $this->drawnBoxes[] = array(
            'pos' => $pos,
            'box' => $box
        );
    }

    public function reset()
    {
        $this->drawnBoxes = array();
    }

    public function overlaps(PointInterface $pos, BoxInterface $box)
    {
        foreach ($this->drawnBoxes as $dBox) {
            if ($this->intersects($pos, $box, $dBox['pos'], $dBox['box'])) {
                return true;
            }
        }
        return false;
    }

    protected function intersects(
        PointInterface $pos1, BoxInterface $box1,
        PointInterface $pos2, BoxInterface $box2
    ) {
        if ($pos1->getY() + $box1->getHeight() < $pos2->getY()) {
            return false;
        }
        if ($pos1->getY() > $pos2->getY() + $box2->getHeight()) {
            return false;
        }
        if ($pos1->getX() > $pos2->getX() + $box2->getWidth()) {
            return false;
        }
        if ($pos1->getX() + $box1->getWidth() < $pos2->getX()) {
            return false;
        }

        return true;
    }
}
