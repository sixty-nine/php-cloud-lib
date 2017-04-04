<?php

namespace SixtyNine\Cloud\Renderer;


use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use SixtyNine\Cloud\Drawer\Drawer;
use SixtyNine\Cloud\Factory\FontsFactory;
use SixtyNine\Cloud\Model\Cloud;
use SixtyNine\Cloud\Placer\PlacerInterface;

class CloudRenderer
{
    /**
     * @param Cloud $cloud
     * @param \SixtyNine\Cloud\Factory\FontsFactory $fontsFactory
     * @param bool $drawBoundingBoxes
     * @return \Imagine\Gd\Image|\Imagine\Image\ImageInterface
     */
    public function render(Cloud $cloud, FontsFactory $fontsFactory, $drawBoundingBoxes = false)
    {
        $drawer = Drawer::create($fontsFactory)
            ->createImage($cloud->getWidth(), $cloud->getHeight(), $cloud->getBackgroundColor())
            ->setFont($cloud->getFont())
        ;

        /** @var \SixtyNine\Cloud\Model\CloudWord $word */
        foreach ($cloud->getWords() as $word) {

            if (!$word->getIsVisible()) {
                continue;
            }

            $pos = $word->getPosition();
            $box = $word->getBox();

            if ($word->getAngle() === 270) {
                $drawer->drawText($pos[0] + $box->getWidth(), $pos[1] + $box->getHeight() - $word->getSize(), $word->getText(), $word->getSize(), $word->getColor(), $word->getAngle());
            } else {
                $drawer->drawText($pos[0], $pos[1], $word->getText(), $word->getSize(), $word->getColor(), $word->getAngle());
            }

            if ($drawBoundingBoxes) {
                $drawer->drawBox($box->getX(), $box->getY(), $box->getWidth(), $box->getHeight(), '#ff0000');
            }
        }

        return $drawer->getImage();
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
