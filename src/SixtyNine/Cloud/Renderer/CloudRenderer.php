<?php

namespace SixtyNine\Cloud\Renderer;


use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use SixtyNine\Cloud\Factory\FontsFactory;
use SixtyNine\Cloud\Model\Cloud;
use SixtyNine\Cloud\Placer\PlacerInterface;

class CloudRenderer
{
    /**
     * @param Cloud $cloud
     * @param bool $drawBoundingBoxes
     * @return \Imagine\Gd\Image|\Imagine\Image\ImageInterface
     */
    public function render(Cloud $cloud, FontsFactory $fontsFactory, $drawBoundingBoxes = false)
    {
        $imagine = new Imagine();
        $size  = new Box($cloud->getWidth(), $cloud->getHeight());
        $image = $imagine->create(
            $size,
            new Color($cloud->getBackgroundColor())
        );

        /** @var \SixtyNine\Cloud\Model\CloudWord $word */
        foreach ($cloud->getWords() as $word) {

            if (!$word->getIsVisible()) {
                continue;
            }

            $font = $fontsFactory->getImagineFont($cloud->getFont(), $word->getSize(), $word->getColor());
            $angle = $word->getAngle();
            $pos = $word->getPosition();
            $box =$word->getBox();

            if ($angle === 0) {
                $image->draw()->text(
                    $word->getText(),
                    $font,
                    new Point($pos[0], $pos[1]),
                    $angle
                );
            } else {
                $image->draw()->text(
                    $word->getText(),
                    $font,
                    new Point($pos[0], $pos[1] - $box[0]),
                    $angle
                );
            }

            if ($drawBoundingBoxes) {
                if ($word->getAngle() === 0) {
                    $points = array(
                        new Point($pos[0], $pos[1]),
                        new Point($pos[0] + $box[0], $pos[1]),
                        new Point($pos[0] + $box[0], $pos[1] + $box[1]),
                        new Point($pos[0], $pos[1] + $box[1]),
                    );
                } else {
                    $points = array(
                        new Point($pos[0], $pos[1]),
                        new Point($pos[0] - $box[0], $pos[1]),
                        new Point($pos[0] - $box[0], $pos[1] - $box[1]),
                        new Point($pos[0], $pos[1] - $box[1]),
                    );
                }
                $image->draw()->polygon($points, new Color(0xFF0000));
            }
        }

        return $image;
    }

    public function renderUsher(
        ImageInterface $image,
        PlacerInterface $placer,
        $color,
        $maxIterations = 5000
    ) {
        $i = 0;
        $cur = $placer->getFirstPlaceToTry();
        $color = new Color($color);

        while($cur) {

            $next = $placer->getNextPlaceToTry($cur);

            if ($next) {
                $image->draw()->line($cur, $next, $color);
            }

            $i++;
            $cur = $next;

            if ($i >= $maxIterations) {
                break;
            }
        }
    }
}
