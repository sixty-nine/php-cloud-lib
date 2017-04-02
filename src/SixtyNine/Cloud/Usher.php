<?php

namespace SixtyNine\Cloud;

use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use Imagine\Image\PointInterface;
use SixtyNine\Cloud\Placer\PlacerInterface;

/**
 * Responsible to find a place for the word in the cloud
 */

class Usher
{
    const DEFAULT_MAX_TRIES = 100000;

    /** @var int */
    protected $maxTries;

    /** @var \SixtyNine\Cloud\Mask */
    protected $mask;

    /** @var \SixtyNine\Cloud\Placer\PlacerInterface */
    protected $placer;

    /**
     * @param int $imgWidth
     * @param int $imgHeight
     * @param PlacerInterface $placer
     * @param int $maxTries
     */
    public function __construct(
        $imgWidth,
        $imgHeight,
        PlacerInterface $placer,
        $maxTries = self::DEFAULT_MAX_TRIES
    ) {
        $this->mask = new Mask();
        $this->imgHeight = $imgHeight;
        $this->imgWidth = $imgWidth;
        $this->maxTries = $maxTries;
        $this->placer = $placer;
    }

    public function getPlace(BoxInterface $box)
    {
        $bounds = new Box($this->imgWidth, $this->imgHeight);
        $place = $this->searchPlace($bounds, $box);

        if ($place) {
            $this->mask->add($place, $box);
            return $place;
        }

        return false;
    }

    /**
     * Search a free place for a new box.
     * @param \Imagine\Image\Box $bounds
     * @param \Imagine\Image\Box|\Imagine\Image\BoxInterface $box $box
     * @return bool|PointInterface
     */
    protected function searchPlace(Box $bounds, BoxInterface $box)
    {
        $place_found = false;
        $current = $this->placer->getFirstPlaceToTry();
        $curTry = 1;

        while (!$place_found) {

            if (!$current) {
                return false;
            }

            if ($curTry > $this->maxTries) {
                return false;
            }

            $place_found = !$this->mask->overlaps($current, $box);

            if ($place_found) {
                break;
            }

            $current = $this->placer->getNextPlaceToTry($current);
            $curTry++;
        }

        return $current->in($bounds) ? $current : false;
    }
}
