<?php

namespace SixtyNine\Cloud\Usher;

use SixtyNine\Cloud\FontMetrics;
use SixtyNine\Cloud\Model\Box;
use SixtyNine\Cloud\Placer\PlacerInterface;

/**
 * Responsible to find a place for the word in the cloud
 */

class Usher
{
    const DEFAULT_MAX_TRIES = 100000;

    /** @var int */
    protected $maxTries;

    /** @var \SixtyNine\Cloud\Usher\MaskInterface */
    protected $mask;

    /** @var \SixtyNine\Cloud\Placer\PlacerInterface */
    protected $placer;

    /** @var \SixtyNine\Cloud\FontMetrics */
    protected $metrics;

    /**
     * @param int $imgWidth
     * @param int $imgHeight
     * @param PlacerInterface $placer
     * @param FontMetrics $metrics
     * @param int $maxTries
     */
    public function __construct(
        $imgWidth,
        $imgHeight,
        PlacerInterface $placer,
        FontMetrics $metrics,
        $maxTries = self::DEFAULT_MAX_TRIES
    ) {
//        $this->mask = new SimpleMask();
        $this->mask = new QuadTreeMask($imgWidth, $imgHeight);
        $this->metrics = $metrics;
        $this->imgHeight = $imgHeight;
        $this->imgWidth = $imgWidth;
        $this->maxTries = $maxTries;
        $this->placer = $placer;
    }

    /**
     * @param string $word
     * @param string $font
     * @param int $size
     * @param int $angle
     * @return bool|Box
     */
    public function getPlace($word, $font, $size, $angle)
    {
        $bounds = new Box(0, 0, $this->imgWidth, $this->imgHeight);
        $box = $this->metrics->calculateSize($word, $font, $size, $angle);
        $place = $this->searchPlace($bounds, $box);

        if ($place) {
            $this->mask->add($place->getPosition(), $box);
            return $place;
        }

        return false;
    }

    /**
     * Search a free place for a new box.
     * @param \SixtyNine\Cloud\Model\Box $bounds
     * @param \SixtyNine\Cloud\Model\Box $box
     * @return bool|Box
     */
    protected function searchPlace(Box $bounds, Box $box)
    {
        $placeFound = false;
        $current = $this->placer->getFirstPlaceToTry();
        $curTry = 1;

        while (!$placeFound) {

            if (!$current) {
                return false;
            }

            if ($curTry > $this->maxTries) {
                return false;
            }

            $currentBox = $box->move($current->getX(), $current->getY());
            $placeFound = !$this->mask->overlaps($currentBox);

            $placeFound = $placeFound &&  $currentBox->inside($bounds);

            if ($placeFound) {
                break;
            }

            $current = $this->placer->getNextPlaceToTry($current);
            $curTry++;
        }

        $currentBox = $box->move($current->getX(), $current->getY());
        return $currentBox->inside($bounds) ? $currentBox : false;
    }
}
