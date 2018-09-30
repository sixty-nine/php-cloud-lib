<?php

namespace SixtyNine\Cloud\Usher;

use SixtyNine\Cloud\FontMetrics;
use SixtyNine\DataTypes\Box;
use Imagine\Image\Point;

class PreciseMask extends QuadTreeMask implements MaskInterface
{
    /**
     * @param int $width
     * @param int $height
     * @param FontMetrics $metrics
     */
    public function __construct($width, $height, FontMetrics $metrics) {
        parent::__construct($width, $height);
        $this->metrics = $metrics;
    }

    /**
     * @param string $word
     * @param Box $place
     * @param string $font
     * @param int $size
     * @param int $angle
     */
    public function addWordToMask($word, Box $place, $font, $size, $angle)
    {
        $base = $this->metrics->calculateSize($word, $font, $size, $angle);
        foreach (str_split($word) as $letter) {
            $box = $this->metrics->calculateSize($letter, $font, $size);

            if ($angle === 0) {
                $newPos = new Point($place->getX(), $place->getY() + ($base->getHeight() - $box->getHeight()));
                $this->add($newPos, $box, $angle);
                $place = $place->move($box->getWidth(), 0);
                continue;
            }

            // Invert width and height
            $box = new Box(0, 0, $box->getHeight(), $box->getWidth());

            if ($place->getY() + ($base->getHeight() - $box->getHeight()) < 0) {
                continue;
            }

            if ($place->getX() + ($base->getWidth() - $box->getWidth()) < 0) {
                continue;
            }

            $newPos = new Point(
                $place->getX() + ($base->getWidth() - $box->getWidth()),
                $place->getY() + ($base->getHeight() - $box->getHeight())
            );
            $this->add($newPos, $box);
            $place = $place->move(0, -$box->getHeight());
        }
    }

}
