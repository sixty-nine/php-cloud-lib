<?php

namespace SixtyNine\Cloud\Model;

use JMS\Serializer\Annotation as JMS;

class CloudWord
{
    /**
     * @var int
     * @JMS\Type("integer")
     */
    protected $size;

    /**
     * @var int
     * @JMS\Type("integer")
     */
    protected $angle;

    /**
     * @var string
     * @JMS\Type("string")
     */
    protected $color;

    /**
     * @var string
     * @JMS\Type("string")
     */
    protected $text;

    /**
     * @var array
     * @JMS\Type("array")
     */
    protected $position;

    /**
     * @var Box
     * @JMS\Type("SixtyNine\Cloud\Model\Box")
     */
    protected $box;

    /**
     * @var bool
     * @JMS\Type("boolean")
     */
    protected $isVisible;

    /**
     * @var Cloud
     * @JMS\Type("SixtyNine\Cloud\Model\Cloud")
     */
    protected $cloud;

    /**
     * Set size
     *
     * @param integer $size
     *
     * @return CloudWord
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param \SixtyNine\Cloud\Model\Cloud $cloud
     * @return $this
     */
    public function setCloud($cloud)
    {
        $this->cloud = $cloud;
        return $this;
    }

    /**
     * @return \SixtyNine\Cloud\Model\Cloud
     */
    public function getCloud()
    {
        return $this->cloud;
    }

    /**
     * @param array $position
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return array
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param boolean $isVisible
     * @return $this
     */
    public function setIsVisible($isVisible)
    {
        $this->isVisible = $isVisible;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsVisible()
    {
        return $this->isVisible;
    }

    /**
     * @param int $angle
     * @return $this
     */
    public function setAngle($angle)
    {
        $this->angle = $angle;
        return $this;
    }

    /**
     * @return int
     */
    public function getAngle()
    {
        return $this->angle;
    }

    /**
     * @param string $color
     * @return $this
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param Box $box
     * @return $this
     */
    public function setBox($box)
    {
        $this->box = $box;
        return $this;
    }

    /**
     * @return array
     */
    public function getBox()
    {
        return $this->box;
    }
}

