<?php

namespace SixtyNine\Cloud\Model;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;

class Cloud
{
    /**
     * @var string
     * @JMS\Type("string")
     */
    protected $backgroundColor;

    /**
     * @var int
     * @JMS\Type("integer")
     */
    protected $width;

    /**
     * @var int
     * @JMS\Type("integer")
     */
    protected $height;

    /**
     * @var string
     * @JMS\Type("string")
     */
    protected $font;

    /**
     * @var ArrayCollection
     * @JMS\Type("ArrayCollection<SixtyNine\Cloud\Model\CloudWord>")
     */
    protected $words;

    public function __construct()
    {
        $this->words = new ArrayCollection();
    }

    /**
     * @param string $backgroundColor
     * @return Cloud
     */
    public function setBackgroundColor($backgroundColor)
    {
        $this->backgroundColor = $backgroundColor;
        return $this;
    }

    /**
     * @return string
     */
    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }

    /**
     * @param string $font
     * @return Cloud
     */
    public function setFont($font)
    {
        $this->font = $font;
        return $this;
    }

    /**
     * @return string
     */
    public function getFont()
    {
        return $this->font;
    }

    /**
     * @param CloudWord $word
     * @return Cloud
     */
    public function addWord(CloudWord $word)
    {
        if (!$this->words->contains($word)) {
            $this->words->add($word);
        }
        return $this;
    }

    /**
     * @param CloudWord $word
     * @return Cloud
     */
    public function removeWord(CloudWord $word)
    {
        $this->words->removeElement($word);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getWords()
    {
        return $this->words;
    }

    /**
     * @param int $height
     * @return Cloud
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $width
     * @return Cloud
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }
}

