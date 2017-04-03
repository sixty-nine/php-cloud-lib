<?php

namespace SixtyNine\Cloud;

use SixtyNine\Cloud\Factory\FontsFactory;
use SixtyNine\Cloud\Model\Box;

class FontMetrics
{
    /** @var \SixtyNine\Cloud\Factory\FontsFactory  */
    protected $fontsFactory;

    /**
     * @param FontsFactory $fontsFactory
     */
    public function __construct(FontsFactory $fontsFactory)
    {
        $this->fontsFactory = $fontsFactory;
    }

    /**
     * @param string $text
     * @param string $font
     * @param int $fontSize
     * @param int $angle
     * @return Box
     */
    public function calculateSize($text, $font, $fontSize, $angle = 0)
    {
        $imagineFont = $this->fontsFactory->getImagineFont($font, $fontSize);
        $imagineBox = $imagineFont->box($text, $angle);
        return new Box(0, 0, $imagineBox->getWidth(), $imagineBox->getHeight());
    }
}
