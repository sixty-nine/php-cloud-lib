<?php

namespace SixtyNine\Cloud\Usher;

use Imagine\Image\Point;
use SixtyNine\Cloud\Drawer\Drawer;
use SixtyNine\Cloud\Factory\Logger;
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

    /** @var Logger */
    protected $logger;

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
        $this->mask = new QuadTreeMask($imgWidth, $imgHeight);
        $this->metrics = $metrics;
        $this->imgHeight = $imgHeight;
        $this->imgWidth = $imgWidth;
        $this->maxTries = $maxTries;
        $this->placer = $placer;
        $this->logger = Logger::getInstance();
    }

    /**
     * @param string $word
     * @param string $font
     * @param int $fontSize
     * @param int $angle
     * @param bool $precise
     * @return bool|Box
     */
    public function getPlace($word, $font, $fontSize, $angle, $precise = false)
    {
        $this->logger->log(
            sprintf(
                'Search place for "%s", font = %s(%s), angle = %s',
                $word,
                str_replace('.ttf', '', $font),
                $fontSize,
                $angle
            ),
            Logger::DEBUG
        );

        $bounds = new Box(0, 0, $this->imgWidth, $this->imgHeight);
        $size = $this->metrics->calculateSize($word, $font, $fontSize);
        $box = Drawer::getBoxFoxText(0, 0, $size->getWidth(), $size->getHeight(), $angle);

        $this->logger->log('  Text dimensions: ' . $size->getDimensions(), Logger::DEBUG);

        $place = $this->searchPlace($bounds, $box);

        if ($place) {
            if ($precise) {
                $this->addWordToMask($word, $place, $font, $fontSize, $angle);
            } else {
                $this->mask->add(new Point(0, 0), $place);
            }
            return $place;
        }

        return false;
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
                $this->mask->add($newPos, $box, $angle);
                $place = $place->move($box->getWidth(), 0);
            } else {
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
                $this->mask->add($newPos, $box);
                $place = $place->move(0, -$box->getHeight());
            }
        }
    }

    /**
     * Search a free place for a new box.
     * @param \SixtyNine\Cloud\Model\Box $bounds
     * @param \SixtyNine\Cloud\Model\Box $box
     * @return bool|Box
     */
    protected function searchPlace(Box $bounds, Box $box)
    {

        $this->logger->log('  Search place for ' . $box, Logger::DEBUG);

        $placeFound = false;
        $current = $this->placer->getFirstPlaceToTry();
        $curTry = 1;
        $currentBox = null;

        while (!$placeFound) {

            if (!$current) {
                return false;
            }

            if ($curTry > $this->maxTries) {
                return false;
            }

            $currentBox = $box->move($current->getX(), $current->getY());

            $outOfBounds = !$currentBox->inside($bounds);

            if (!$outOfBounds) {
                $placeFound = !$this->mask->overlaps($currentBox);
                $placeFound = $placeFound && !$outOfBounds;
            }

            $this->logger->log(sprintf(
                '  Trying %s --> %s',
                $currentBox,
                $outOfBounds ? 'Out of bounds' : ($placeFound ? 'OK' : 'Collision')
            ), Logger::DEBUG);

            if ($placeFound) {
                break;
            }

            $current = $this->placer->getNextPlaceToTry($current);
            $curTry++;
        }

        return $currentBox->inside($bounds) ? $currentBox : false;
    }

    /**
     * @return MaskInterface
     */
    public function getMask()
    {
        return $this->mask;
    }
}
