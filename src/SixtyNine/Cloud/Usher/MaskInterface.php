<?php

namespace SixtyNine\Cloud\Usher;

use Imagine\Image\PointInterface;
use SixtyNine\Cloud\Model\Box;

interface MaskInterface
{
    /**
     * @param Box $box
     * @return bool
     */
    public function overlaps(Box $box);

    /**
     * @param \Imagine\Image\PointInterface $position
     * @param Box $box
     */
    public function add(PointInterface $position, Box $box);
}