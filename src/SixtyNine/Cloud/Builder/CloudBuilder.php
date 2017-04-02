<?php
namespace SixtyNine\Cloud\Builder;

use Imagine\Gd\Font;
use Imagine\Image\Color;
use Imagine\Image\Point;
use SixtyNine\Cloud\Factory\FontsFactory;
use SixtyNine\Cloud\Factory\PlacerFactory;
use SixtyNine\Cloud\FontSize\FontSizeGeneratorInterface;
use SixtyNine\Cloud\FontSize\LinearFontSizeGenerator;
use SixtyNine\Cloud\Model\Cloud;
use SixtyNine\Cloud\Model\CloudWord;
use SixtyNine\Cloud\Model\Word;
use SixtyNine\Cloud\Model\WordsList;
use SixtyNine\Cloud\Usher;

class CloudBuilder
{
    /** @var WordsList */
    protected $list;
    /** @var int */
    protected $width = 800;
    /** @var int */
    protected $height = 600;
    /** @var string */
    protected $font;
    /** @var string */
    protected $backgroundColor = '#000000';
    /** @var FontSizeGeneratorInterface */
    protected $sizeGenerator;
    /** @var string */
    protected $placerName;
    /** @var int */
    protected $minFontSize = 10;
    /** @var int */
    protected $maxFontSize = 60;
    /** @var FontsFactory */
    protected $fontsFactory;

    protected function __construct(FontsFactory $fontsFactory)
    {
        $this->fontsFactory = $fontsFactory;
    }

    /**
     * @return CloudBuilder
     */
    public static function create(FontsFactory $fontsFactory)
    {
        return new self($fontsFactory);
    }

    /**
     * @param int $width
     * @param int $height
     * @return CloudBuilder
     */
    public function setDimension($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
        return $this;
    }

    public function setBackgroundColor($color)
    {
        $this->backgroundColor = $color;
        return $this;
    }

    public function setFont($name)
    {
        $this->font = $name;
        return $this;
    }

    public function setSizeGenerator(FontSizeGeneratorInterface $generator)
    {
        $this->sizeGenerator = $generator;
        return $this;
    }

    public function setFontSizes($minSize, $maxSize)
    {
        $this->minFontSize = $minSize;
        $this->maxFontSize = $maxSize;
        return $this;
    }

    public function setPlacer($name)
    {
        if (!array_key_exists($name, PlacerFactory::getInstance()->getPlacersNames())) {
            throw new \InvalidArgumentException('Placer not found: ' . $name);
        }

        $this->placerName = $name;
        return $this;
    }

    public function useList(WordsList $list)
    {
        $this->list = $list;
        return $this;
    }

    public function build()
    {
        if (!$this->font) {
            throw new \InvalidArgumentException('Font not set');
        }

        $cloud = new Cloud();
        $cloud
            ->setFont($this->font)
            ->setWidth($this->width)
            ->setHeight($this->height)
            ->setBackgroundColor($this->backgroundColor)
        ;

        if ($this->list) {
            $this->addWords($cloud, $this->list);
            $this->placeWords($cloud);
        }

        return $cloud;
    }

    protected function addWords(Cloud $cloud, WordsList $list)
    {
        $words = $list->getWordsOrdered();
        $maxCount = $list->getWordsMaxCount();

        if (!$this->sizeGenerator) {
            $this->sizeGenerator = new LinearFontSizeGenerator();
        }

        /** @var Word $word */
        foreach ($words as $word) {
            $cloudWord = new CloudWord();
            $cloudWord
                ->setCloud($cloud)
                ->setPosition(array(0, 0))
                ->setSize($this->sizeGenerator->calculateFontSize($word->getCount(), $maxCount, $this->minFontSize, $this->maxFontSize))
                ->setAngle($word->getOrientation() === Word::DIR_VERTICAL ? 270 : 0)
                ->setColor($word->getColor())
                ->setText($word->getText())
                ->setIsVisible(true)
            ;
            $cloud->addWord($cloudWord);
        }

        return $this;
    }

    protected function placeWords(Cloud $cloud)
    {
        $placer = $this->placerName
            ? PlacerFactory::getInstance()->getPlacer($this->placerName, $cloud->getWidth(), $cloud->getHeight())
            : PlacerFactory::getInstance()->getDefaultPlacer($cloud->getWidth(), $cloud->getHeight())
        ;

        $usher = new Usher($cloud->getWidth(), $cloud->getHeight(), $placer);

        /** @var CloudWord $word */
        foreach ($cloud->getWords() as $word) {
            $font = $this->fontsFactory->getImagineFont($cloud->getFont(), $word->getSize(), $word->getColor());
            $box = $font->box($word->getText(), $word->getAngle());
            $place = $usher->getPlace($box);

            $word
                ->setIsVisible((bool)$place)
                ->setBox(array($box->getWidth(), $box->getHeight()))
            ;

            if ($place) {

                if ($word->getAngle() !== 0) {
                    $place = new Point(
                        $place->getX() + $box->getWidth(),
                        $place->getY() + $box->getHeight()
                    );
                }

                $word->setPosition(array((int)$place->getX(), (int)$place->getY()));
            }
        }
    }

}
