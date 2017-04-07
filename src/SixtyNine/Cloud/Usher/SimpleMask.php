<?php

namespace SixtyNine\Cloud\Usher;

use Imagine\Image\PointInterface;
use SixtyNine\DataTypes\Box;

class SimpleMask
{
    /** @var Box[] */
    protected  $boundingBoxes = array();

    /**
     * @param \Imagine\Image\PointInterface $position
     * @param Box $box
     */
    public function add(PointInterface $position, Box $box)
    {
        $box = $box->move($position->getX(), $position->getY());
        $this->boundingBoxes[] = $box;
    }

    /**
     * @param Box $box
     * @return bool
     */
    public function overlaps(Box $box)
    {
        foreach ($this->boundingBoxes as $dBox) {
            if ($box->intersects($dBox)) {
                return true;
            }
        }
        return false;
    }
}
