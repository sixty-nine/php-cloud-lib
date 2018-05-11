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
use SixtyNine\Cloud\Usher\MaskInterface;

class CloudRenderer
{
    /** @var Drawer */
    protected $drawer;
    /** @var Cloud */
    protected $cloud;

    /**
     * @param Cloud $cloud
     * @param FontsFactory $fontsFactory
     */
    public function __construct(Cloud $cloud, FontsFactory $fontsFactory)
    {
        $this->cloud = $cloud;
        $this->drawer = Drawer::create($fontsFactory)
            ->createImage($cloud->getWidth(), $cloud->getHeight(), $cloud->getBackgroundColor(), $cloud->getBackgroundOpacity())
            ->setFont($cloud->getFont())
        ;
    }

    public function renderCloud()
    {
        /** @var \SixtyNine\Cloud\Model\CloudWord $word */
        foreach ($this->cloud->getWords() as $word) {

            if (!$word->getIsVisible()) {
                continue;
            }

            $pos = $word->getPosition();
            $box = $word->getBox();

            if ($word->getAngle() === 270) {
                $this->drawer->drawText($pos[0] + $box->getWidth(), $pos[1] + $box->getHeight() - $word->getSize(), $word->getText(), $word->getSize(), $word->getColor(), $word->getAngle());
            } else {
                $this->drawer->drawText($pos[0], $pos[1], $word->getText(), $word->getSize(), $word->getColor(), $word->getAngle());
            }
        }
    }

    /**
     * @param string $color
     */
    public function renderBoundingBoxes($color = '#ff0000')
    {
        /** @var \SixtyNine\Cloud\Model\CloudWord $word */
        foreach ($this->cloud->getWords() as $word) {

            if (!$word->getIsVisible()) {
                continue;
            }

            $box = $word->getBox();

            $this->drawer->drawBox($box->getX(), $box->getY(), $box->getWidth(), $box->getHeight(), $color);
        }
    }

    /**
     * @param PlacerInterface $placer
     * @param string $color
     * @param int $maxIterations
     */
    public function renderUsher(
        PlacerInterface $placer,
        $color = '#ff0000',
        $maxIterations = 5000
    ) {
        $i = 0;
        $cur = $placer->getFirstPlaceToTry();
        $color = new Color($color);

        while($cur) {

            $next = $placer->getNextPlaceToTry($cur);

            if ($next) {
                $this->getImage()->draw()->line($cur, $next, $color);
            }

            $i++;
            $cur = $next;

            if ($i >= $maxIterations) {
                break;
            }
        }
    }

    /**
     * @param MaskInterface $mask
     * @param string $color
     */
    public function renderMask(MaskInterface $mask, $color = '#ff0000')
    {
        $this->drawer->drawMask($mask, $color);
    }

    /**
     * @return ImageInterface
     */
    public function getImage()
    {
        return $this->drawer->getImage();
    }
}
