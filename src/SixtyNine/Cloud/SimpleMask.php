<?php

namespace SixtyNine\Cloud;

use Imagine\Image\PointInterface;
use SixtyNine\Cloud\Model\Box;
use SixtyNine\Cloud\Model\Word;

class SimpleMask
{
    /** @var Box[] */
    protected  $boundingBoxes = array();

    /**
     * @param \Imagine\Image\PointInterface $position
     * @param Model\Box $box
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
