<?php
namespace SixtyNine\Cloud\Builder;

use Imagine\Gd\Font;
use Imagine\Image\Point;
use SixtyNine\Cloud\Factory\FontsFactory;
use SixtyNine\Cloud\Factory\PlacerFactory;
use SixtyNine\Cloud\FontMetrics;
use SixtyNine\Cloud\FontSize\FontSizeGeneratorInterface;
use SixtyNine\Cloud\FontSize\LinearFontSizeGenerator;
use SixtyNine\Cloud\Model\Cloud;
use SixtyNine\Cloud\Model\CloudWord;
use SixtyNine\Cloud\Model\Word;
use SixtyNine\Cloud\Model\WordsList;
use SixtyNine\Cloud\Usher\MaskInterface;
use SixtyNine\Cloud\Usher\Usher;
use Webmozart\Assert\Assert;

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
    protected $backgroundColor = '#ffffff';
    /** @var int */
    protected $backgroundOpacity = 100;
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
    /** @var bool */
    protected $precise = false;
    /** @var MaskInterface */
    protected $mask;

    /**
     * @param FontsFactory $fontsFactory
     */
    protected function __construct(FontsFactory $fontsFactory)
    {
        $this->fontsFactory = $fontsFactory;
    }

    /**
     * @param \SixtyNine\Cloud\Factory\FontsFactory $fontsFactory
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

    /**
     * @param string $color
     * @return $this
     */
    public function setBackgroundColor($color)
    {
        $this->backgroundColor = $color;
        return $this;
    }

    /**
     * @param int $opacity
     * @return $this
     */
    public function setBackgroundOpacity($opacity)
    {
        $this->backgroundOpacity = $opacity;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setFont($name)
    {
        $this->font = $name;
        return $this;
    }

    /**
     * @param FontSizeGeneratorInterface $generator
     * @return $this
     */
    public function setSizeGenerator(FontSizeGeneratorInterface $generator)
    {
        $this->sizeGenerator = $generator;
        return $this;
    }

    /**
     * @param int $minSize
     * @param int $maxSize
     * @return $this
     */
    public function setFontSizes($minSize, $maxSize)
    {
        $this->minFontSize = $minSize;
        $this->maxFontSize = $maxSize;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setPlacer($name)
    {
        Assert::oneOf($name, PlacerFactory::getInstance()->getPlacersNames(), 'Placer not found: ' . $name);
        $this->placerName = $name;
        return $this;
    }

    /**
     * @return $this
     */
    public function setPrecise()
    {
        $this->precise = true;
        return $this;
    }

    /**
     * @param WordsList $list
     * @return $this
     */
    public function useList(WordsList $list)
    {
        $this->list = $list;
        return $this;
    }

    /**
     * @return Cloud
     */
    public function build()
    {
        Assert::notNull($this->font, 'Font not set');

        $cloud = new Cloud();
        $cloud
            ->setFont($this->font)
            ->setWidth($this->width)
            ->setHeight($this->height)
            ->setBackgroundColor($this->backgroundColor)
            ->setBackgroundOpacity($this->backgroundOpacity)
        ;

        if ($this->list) {
            $this->addWords($cloud, $this->list);
            $this->mask = $this->placeWords($cloud);
        }

        return $cloud;
    }

    /**
     * @param Cloud $cloud
     * @param WordsList $list
     * @return $this
     */
    protected function addWords(Cloud $cloud, WordsList $list)
    {
        $words = $list->getWordsOrdered();
        $maxCount = $list->getWordsMaxCount();

        if (!$this->sizeGenerator) {
            $this->sizeGenerator = new LinearFontSizeGenerator();
        }

        /** @var Word $word */
        foreach ($words as $word) {

            if (!$word->getCount()) {
                continue;
            }

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

    /**
     * @param Cloud $cloud
     * @return \SixtyNine\Cloud\Usher\Usher
     */
    protected function placeWords(Cloud $cloud)
    {
        $placer = $this->placerName
            ? PlacerFactory::getInstance()->getPlacer($this->placerName, $cloud->getWidth(), $cloud->getHeight())
            : PlacerFactory::getInstance()->getDefaultPlacer($cloud->getWidth(), $cloud->getHeight())
        ;

        $metrics = new FontMetrics($this->fontsFactory);
        $usher = new Usher($cloud->getWidth(), $cloud->getHeight(), $placer, $metrics, $this->precise);

        /** @var CloudWord $word */
        foreach ($cloud->getWords() as $word) {

            $place = $usher->getPlace($word->getText(), $cloud->getFont(), $word->getSize(), $word->getAngle());

            $word->setIsVisible((bool)$place);

            if ($place) {
                $word->setBox($place);
                $word->setPosition(array((int)$place->getX(), (int)$place->getY()));
            }
        }

        return $usher->getMask();
    }

    /**
     * @return MaskInterface
     */
    public function getMask()
    {
        return $this->mask;
    }
}
